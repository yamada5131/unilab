# Quy trình hoạt động của UniLab

Hệ thống UniLab hoạt động thông qua các luồng xử lý chính như sau:

## 1. Thiết lập ban đầu và cài đặt Agent

```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant S as Laravel Server
    participant DB as Database
    participant DP as Deployment Tools<br>(GP/SCCM/Ansible)
    participant A as Agent(s)

    %% Thêm máy tính vào hệ thống
    U->>+D: Thêm máy mới hoặc import danh sách máy
    D->>+S: POST /computers<br>(kèm thông tin phòng, vị trí, MAC address)
    activate S
    S->>S: Sinh secret key cho mỗi máy<br>sk_[random16chars]
    S->>+DB: Lưu máy + hash của secret key
    deactivate DB
    S-->>-D: Trả về danh sách máy đã thêm
    deactivate S

    %% Tạo installation scripts
    U->>+D: Tạo scripts cài đặt
    D->>+S: GET /api/computers/{id}/installation-script
    S->>+DB: Lấy thông tin máy
    DB-->>-S: Trả về thông tin máy
    S->>S: Sinh script với thông tin định danh:<br>- computer_id<br>- MAC address<br>- room_id<br>- location<br>- secret_key (plaintext)
    S-->>-D: Trả về scripts (PowerShell/Bash)
    D-->>-U: Hiển thị scripts

    %% Triển khai cài đặt
    alt Cài đặt trực tiếp
        U->>+A: Chạy script trên từng máy tính
    else Triển khai từ xa
        U->>+DP: Upload scripts + cấu hình triển khai
        DP->>+A: Triển khai scripts đến nhiều máy
        deactivate DP
    end

    %% Cài đặt Agent
    activate A
    A->>A: Tải Agent từ repository
    A->>A: Tạo file cấu hình với thông tin định danh
    A->>A: Cài đặt Agent service
    A->>A: Khởi động service
    Note over A: Agent đã được cài đặt với<br>thông tin định danh và secret_key
    deactivate A
```

## 2. Đăng ký và xác thực Agent

```mermaid
sequenceDiagram
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database

    A->>+S: POST /api/agent/register<br>(gửi MAC, computer_id, secret_key, và thông tin phần cứng)
    S->>+DB: Xác minh thông tin định danh<br>và cập nhật thông tin phần cứng
    DB-->>-S: Kết quả xác minh
    alt Tìm thấy máy tính
        S-->>A: 200 OK - Trả về thông tin cấu hình cơ bản
        Note over S,A: Thông tin bao gồm:<br>- Computer ID<br>- Room ID/Name<br>- Polling interval<br>- Logging level<br>- Registration token tạm thời
    else Không tìm thấy
        S-->>-A: 404 Not Found - Máy chưa được đăng ký
        Note over A: Agent sẽ thử lại sau một khoảng thời gian (5 phút)
    end

    A->>+S: POST /api/agent/token<br>(kèm MAC, hostname và registration token)
    Note right of S: Server kiểm tra thông tin xác thực<br>và tạo token cho Agent
    S->>+DB: Lưu token và thông tin kết nối
    DB-->>-S: Xác nhận lưu thành công
    S-->>-A: Trả về token (personal access token), computer_id, và<br>thông tin kết nối MQ (host, port, credentials, routing_key)
    activate A
    Note over A: Agent lưu token và cấu hình<br>để sử dụng cho các request sau
    Note over A: Routing key có dạng:<br>commands.room_{room_id}.computer_{id}
    deactivate A
```

## 3. Cơ chế heartbeat và giám sát

```mermaid
sequenceDiagram
    participant A as Agent
    participant S as Laravel Server
    participant DB as Database

    loop Mỗi 5 phút
        A->>+S: POST /api/agent/heartbeat<br>(kèm thông tin status, CPU, RAM, disk usage)
        Note over A,S: Gửi kèm token trong header<br>Authorization: Bearer {token}
        S->>+DB: Cập nhật trạng thái online và thông tin máy
        DB-->>-S: Xác nhận cập nhật
        S-->>-A: 200 OK hoặc cấu hình mới (nếu có)
    end
```

## 4. Xử lý và thực thi lệnh

```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant S as Laravel Server
    participant DB as Database
    participant MQ as Message Queue
    participant W as Laravel Worker
    participant A as Agent

    U->>+D: Tạo lệnh cho máy tính
    D->>+S: POST /api/commands<br>(Create Command cho 1 hoặc nhiều máy)
    S->>S: Validate request data
    S->>+DB: Insert command record<br>(status="pending", type=SHUTDOWN/INSTALL/etc.)
    DB-->>-S: Xác nhận lưu thành công
    S->>+MQ: Enqueue job into Laravel Queue (SendCommandJob)
    MQ-->>-S: Job đã được enqueue
    S-->>-D: Xác nhận tạo lệnh thành công
    D-->>-U: Thông báo lệnh đã được tạo

    MQ->>+W: Dequeue SendCommandJob
    W->>+DB: Retrieve command record
    DB-->>-W: Thông tin command
    W->>+MQ: Publish command message<br>to RabbitMQ channel (với routing key tương ứng)
    Note right of W: Command format:<br>{id, type, params, payload}
    MQ-->>-W: Message đã được publish
    deactivate W

    A->>+MQ: Subscribe for command messages<br>(dựa trên routing key của máy/phòng)
    MQ-->>-A: Deliver command message

    activate A
    A->>A: Execute command<br>(SHUTDOWN, INSTALL, UPDATE, etc.)
    Note over A: Timeout sau 10 phút<br>nếu lệnh không hoàn thành
    A->>+S: POST /api/agent/command_result<br>(command_id, status=done/error, message)
    Note over A,S: Gửi kèm token trong header<br>Authorization: Bearer {token}
    deactivate A
    S->>+DB: Update command record<br>(status, completed_at, result_message)
    DB-->>-S: Xác nhận cập nhật thành công
    S-->>-A: 200 OK
```

## 5. Xem kết quả thực thi

```mermaid
sequenceDiagram
    actor U as Admin User
    participant D as Dashboard (Web UI)
    participant S as Laravel Server
    participant DB as Database

    U->>+D: Xem kết quả thực thi lệnh
    D->>+S: GET /api/commands/{id}
    S->>+DB: Query command details
    DB-->>-S: Thông tin lệnh và kết quả
    S-->>-D: Trả về thông tin lệnh và kết quả
    D-->>-U: Hiển thị kết quả thực thi
```

## 6. Cơ chế Cập nhật Tự động

Luồng cập nhật phiên bản:

```mermaid
sequenceDiagram
    participant A as Agent
    participant S as Laravel Server
    participant MQ as RabbitMQ
    participant R as Repository

    S->>MQ: Publish update command (fanout)
    Note right of S: Khi phát hiện phiên bản mới
    MQ->>A: Deliver update message
    A->>R: Check package metadata
    R-->>A: Return latest version info
    alt Có bản mới
        A->>R: Download update package
        A->>A: Verify checksum
        A->>A: Apply update
        A->>S: POST /api/agent/update_result
    else Đã là phiên bản mới nhất
        A->>A: Skip update
    end
```
