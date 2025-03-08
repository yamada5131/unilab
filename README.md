# UniLab - Hệ thống quản lý phòng máy thực hành

## Giới thiệu
UniLab là một hệ thống quản lý phòng máy thực hành cho trường đại học/cao đẳng, cung cấp khả năng giám sát và điều khiển máy tính từ xa thông qua giao diện web.

## Kiến trúc hệ thống

### Thành phần chính
- **Dashboard (Web UI)**: Giao diện người dùng cho admin
- **Laravel Server**: Backend API server
- **Agent**: Phần mềm chạy trên máy tính client
- **Message Queue**: RabbitMQ để truyền lệnh từ server đến agent
- **Database**: Lưu trữ thông tin phòng, máy, lệnh và kết quả

### Luồng hoạt động của hệ thống

Hệ thống UniLab hoạt động thông qua các luồng xử lý chính như sau:

#### 1. Thiết lập ban đầu và cài đặt Agent
```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database

    U->>D: Thêm máy mới vào phòng
    D->>S: POST /computers<br>(kèm thông tin phòng, vị trí, MAC address)
    S->>DB: Lưu máy cùng thông tin phòng, vị trí
    
    U->>D: Tạo script cài đặt cho Agent
    D->>D: Sinh script cài đặt với thông tin định danh<br>(computer_id, MAC, secret_key)
    U->>A: Chạy script trên máy tính đích
    Note over A: Script cài đặt Agent với<br>thông tin định danh có sẵn
```

#### 2. Đăng ký và xác thực Agent
```mermaid
sequenceDiagram
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database

    A->>S: POST /api/agent/register<br>(gửi MAC, computer_id, secret_key, và thông tin phần cứng)
    S->>DB: Xác minh thông tin định danh<br>và cập nhật thông tin phần cứng
    alt Tìm thấy máy tính
        S-->>A: 200 OK - Trả về thông tin cấu hình cơ bản
        Note over S,A: Thông tin bao gồm:<br>- Computer ID<br>- Room ID/Name<br>- Polling interval<br>- Logging level<br>- Registration token tạm thời
    else Không tìm thấy
        S-->>A: 404 Not Found - Máy chưa được đăng ký
        Note over A: Agent sẽ thử lại sau một khoảng thời gian (5 phút)
    end
    
    A->>S: POST /api/agent/token<br>(kèm MAC, hostname và registration token)
    Note right of S: Server kiểm tra thông tin xác thực<br>và tạo token cho Agent
    S-->>A: Trả về token (personal access token), computer_id, và<br>thông tin kết nối MQ (host, port, credentials, routing_key)
    Note over A: Agent lưu token và cấu hình<br>để sử dụng cho các request sau
    Note over A: Routing key có dạng:<br>commands.room_{room_id}.computer_{id}
```

#### 3. Cơ chế heartbeat và giám sát
```mermaid
sequenceDiagram
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database
    
    loop Mỗi 5 phút
        A->>S: POST /api/agent/heartbeat<br>(kèm thông tin status, CPU, RAM, disk usage)
        Note over A,S: Gửi kèm token trong header<br>Authorization: Bearer {token}
        S->>DB: Cập nhật trạng thái online và thông tin máy
        S-->>A: 200 OK hoặc cấu hình mới (nếu có)
    end
```

#### 4. Xử lý và thực thi lệnh
```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database
    participant MQ as Message Queue
    participant W as Laravel Worker

    U->>D: Tạo lệnh cho máy tính
    D->>S: POST /api/commands<br>(Create Command cho 1 hoặc nhiều máy)
    S->>S: Validate request data
    S->>DB: Insert command record<br>(status="pending", type=SHUTDOWN/INSTALL/etc.)
    S->>S: Dispatch Job (SendCommandJob)
    S->>MQ: Enqueue job into Laravel Queue
    
    W->>MQ: Dequeue SendCommandJob
    W->>DB: Retrieve command record
    W->>MQ: Publish command message<br>to RabbitMQ channel (với routing key tương ứng)
    Note right of W: Command format:<br>{id, type, params, payload}

    A->>MQ: Subscribe for command messages<br>(dựa trên routing key của máy/phòng)
    MQ-->>A: Deliver command message
    
    A->>A: Execute command<br>(SHUTDOWN, INSTALL, UPDATE, etc.)
    Note over A: Timeout sau 10 phút<br>nếu lệnh không hoàn thành
    A->>S: POST /api/agent/command_result<br>(command_id, status=done/error, message)
    Note over A: Gửi kèm token trong header<br>Authorization: Bearer {token}
    S->>DB: Update command record<br>(status, completed_at, result_message)
    S-->>A: 200 OK
```

#### 5. Xem kết quả thực thi
```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant S as Laravel Server
    participant DB as Database

    U->>D: Xem kết quả thực thi lệnh
    D->>S: GET /api/commands/{id}
    S->>DB: Query command details
    S-->>D: Trả về thông tin lệnh và kết quả
    D-->>U: Hiển thị kết quả thực thi
```

## Chi tiết API Endpoints

### 1. API Đăng ký và Xác thực Agent

#### **POST /api/agent/register**
   - **Mô tả**: Agent đăng ký với hệ thống khi khởi động đầu tiên
   - **Request**:
     ```json
     {
       "mac_address": "00:1B:44:11:3A:B7",
       "hostname": "LAB-PC-42",
       "os": "Windows 10 Pro 22H2",
       "specs": {
         "cpu": "Intel Core i5-10400",
         "ram": "16GB",
         "disk": "500GB SSD",
         "gpu": "Intel UHD Graphics 630"
       }
     }
     ```
   - **Response Success** (200 OK):
     ```json
     {
       "success": true,
       "computer_id": "550e8400-e29b-41d4-a716-446655440000",
       "room": {
         "id": "123",
         "name": "Lab A1-404"
       },
       "config": {
         "polling_interval": 300,
         "logging_level": "info"
       },
       "registration_token": "temp_token_for_next_step"
     }
     ```
   - **Response Failure** (404 Not Found):
     ```json
     {
       "success": false,
       "message": "Computer not found in system",
       "retry_after": 300
     }
     ```

#### **POST /api/agent/token**
   - **Mô tả**: Lấy token xác thực cho Agent sau khi đăng ký thành công
   - **Request**:
     ```json
     {
       "mac_address": "00:1B:44:11:3A:B7",
       "hostname": "LAB-PC-42",
       "registration_token": "temp_token_for_next_step"
     }
     ```
   - **Response** (200 OK):
     ```json
     {
       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
       "computer_id": "550e8400-e29b-41d4-a716-446655440000",
       "mq_config": {
         "host": "rabbitmq.example.com",
         "port": 5672,
         "username": "agent_user",
         "password": "********",
         "virtual_host": "/",
         "exchange": "commands",
         "routing_key": "commands.room_123.computer_550e8400"
       }
     }
     ```

### 2. API Heartbeat và Trạng Thái

#### **POST /api/agent/heartbeat**
   - **Mô tả**: Agent gửi trạng thái định kỳ
   - **Headers**: `Authorization: Bearer {token}`
   - **Request**:
     ```json
     {
       "computer_id": "550e8400-e29b-41d4-a716-446655440000",
       "status": "online",
       "resources": {
         "cpu_usage": 15.5,
         "ram_usage": 4567452672,
         "ram_total": 17179869184,
         "disk_usage": 250500000000,
         "disk_total": 500000000000
       },
       "agent_version": "1.0.5",
       "uptime": 18542
     }
     ```
   - **Response** (200 OK):
     ```json
     {
       "success": true,
       "config_changed": false
     }
     ```

### 3. API Kết quả Lệnh

#### **POST /api/agent/command_result**
   - **Mô tả**: Agent gửi kết quả thực thi lệnh
   - **Headers**: `Authorization: Bearer {token}`
   - **Request**:
     ```json
     {
       "command_id": "a1b2c3d4-e5f6-4a5b-8c7d-9e0f1a2b3c4d",
       "status": "done", // hoặc "error"
       "message": "Command executed successfully"
     }
     ```
   - **Response** (200 OK):
     ```json
     {
       "success": true,
       "message": "Command status updated successfully"
     }
     ```

## Các loại lệnh hỗ trợ

1. **SHUTDOWN**: Tắt máy tính
   - Params: `{ "delay": 60, "force": false }`

2. **RESTART**: Khởi động lại máy tính
   - Params: `{ "delay": 30, "force": false }`

3. **INSTALL**: Cài đặt phần mềm
   - Params: `{ "package": "vscode", "version": "latest" }`

4. **UPDATE**: Cập nhật hệ thống/phần mềm
   - Params: `{ "target": "system" }` hoặc `{ "target": "application", "name": "chrome" }`

5. **EXECUTE**: Thực thi lệnh/script
   - Params: `{ "command": "ipconfig /flushdns", "shell": "cmd" }`

## Xử lý lỗi và phục hồi

1. **Mất kết nối**:
   - Agent sẽ lưu cache lệnh chưa hoàn thành
   - Tự động kết nối lại sau 30 giây và tiếp tục thực thi

2. **Lệnh thất bại**:
   - Thử lại tối đa 3 lần với các lệnh quan trọng
   - Ghi log chi tiết lỗi và gửi về server

3. **Token hết hạn**:
   - Tự động làm mới token khi gặp lỗi 401 Unauthorized
   - Quay lại quy trình đăng ký nếu không thể làm mới token

### Roadmap:
- [x] ~~TODO: Xây dựng API nhận kết quả từ Agent~~ (Đã hoàn thành)
- [ ] TODO: Xây dựng API đăng ký Agent và xác thực
- [ ] TODO: Xây dựng cơ chế heartbeat để theo dõi trạng thái máy
- [ ] TODO: Hoàn thiện hệ thống phân phối lệnh qua Message Queue
