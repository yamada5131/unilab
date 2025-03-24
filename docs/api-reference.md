# API Reference - UniLab

## 1. API Đăng ký và Xác thực Agent

### 1.1 **POST /api/agent/register**

- **Mô tả**: Agent đăng ký với hệ thống khi khởi động đầu tiên
- **Request**:

  ```json
  {
      "computer_id": "550e8400-e29b-41d4-a716-446655440000",
      "mac_address": "00:1B:44:11:3A:B7",
      "secret_key": "sk_wAb5DcE2fG3hI4jK5",
      "hostname": "LAB-PC-42",
      "os": {
          "name": "Windows",
          "version": "10 Pro",
          "build": "22H2"
      },
      "specs": {
          "cpu": "Intel Core i5-10400",
          "cpu_cores": 6,
          "cpu_threads": 12,
          "ram_total": 17179869184,
          "disk_total": 500107862016,
          "disk_free": 213546065920,
          "gpu": "Intel UHD Graphics 630"
      },
      "agent_version": "1.0.5",
      "installation_timestamp": 1709125482
  }
  ```

- **Quy trình xác minh**:

  ```php
  // Quy trình xác minh (Laravel)
  $computer = Computer::find($request->computer_id);

  if (!$computer ||
      $computer->mac_address !== $request->mac_address ||
      !Hash::check($request->secret_key, $computer->secret_key)) {
      return response()->json(['error' => 'Invalid identification'], 401);
  }

  // Đánh dấu secret_key đã được sử dụng
  $computer->secret_key_used = true;
  $computer->save();
  ```

- **Response Success** (200 OK):

  ```json
  {
      "success": true,
      "room": {
          "id": "123",
          "name": "Lab A1-404"
      },
      "config": {
          "polling_interval": 300,
          "logging_level": "info",
          "client_update": {
              "available": false,
              "version": null
          }
      },
      "registration_token": "temp_token_for_next_step",
      "token_expires_at": "2025-03-09T12:30:00Z"
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

### 1.2 **POST /api/agent/token**

- **Mô tả**: Lấy token xác thực cho Agent sau khi đăng ký thành công
- **Request**:

  ```json
  {
      "registration_token": "temp_token_for_next_step",
      "computer_id": "550e8400-e29b-41d4-a716-446655440000",
      "mac_address": "00:1B:44:11:3A:B7",
      "hostname": "LAB-PC-42"
  }
  ```

- **Response** (200 OK):

  ```json
  {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
      "token_expires_at": "2025-04-09T00:00:00Z",
      "mq_config": {
          "host": "rabbitmq.example.com",
          "port": 5672,
          "username": "agent_user",
          "password": "********",
          "virtual_host": "/",
          "exchange": "commands",
          "routing_key": "commands.room_123.computer_550e8400"
      },
      "api_endpoints": {
          "heartbeat": "/api/agent/heartbeat",
          "command_result": "/api/agent/command_result",
          "token_refresh": "/api/agent/refresh_token"
      }
  }
  ```

### 1.3 **Ý nghĩa của các tham số cấu hình**

#### **Config từ server**

Các tham số cấu hình này đóng vai trò quan trọng trong việc kiểm soát hành vi của Agent:

1. **`polling_interval: 300`**

    - **Đơn vị**: Giây
    - **Tác dụng**: Xác định tần suất Agent gửi heartbeat đến server
    - **Ý nghĩa thực tế**: Mỗi 5 phút (300 giây), Agent sẽ gửi thông tin trạng thái lên server
    - **Lợi ích**:
        - Server biết máy tính có đang hoạt động hay không
        - Giảm tải cho server và mạng (so với heartbeat quá thường xuyên)
        - Có thể điều chỉnh tùy theo nhu cầu giám sát (giảm xuống khi cần giám sát chặt chẽ)

2. **`logging_level: "info"`**

    - **Tác dụng**: Điều khiển mức độ chi tiết của thông tin log từ Agent
    - **Các mức có thể có**:
        - `debug`: Mọi thông tin, chi tiết nhất (hữu ích khi gỡ lỗi)
        - `info`: Thông tin hoạt động thông thường
        - `warning`: Chỉ ghi cảnh báo và lỗi
        - `error`: Chỉ ghi lỗi nghiêm trọng
    - **Lợi ích**: Giúp quản lý kích thước file log và tìm kiếm thông tin quan trọng

3. **`client_update`**
    - **Tác dụng**: Cơ chế tự động cập nhật Agent
    - **Các tham số con**:
        - `available: false`: Hiện không có bản cập nhật mới
        - `version: null`: Không có phiên bản mới nào được xác định
    - **Khi có cập nhật**:

        ```json
        "client_update": {
          "available": true,
          "version": "1.0.6",
          "download_url": "https://unilab.example.com/downloads/agent/1.0.6/windows",
          "force_update": false,
          "changelog": "Cải thiện bảo mật, sửa lỗi kết nối"
        }
        ```

    - **Lợi ích**:
        - Quản lý phiên bản phần mềm tập trung
        - Tự động cập nhật không cần thủ công
        - Đảm bảo tất cả Agent đều chạy phiên bản mới nhất

Server có thể thay đổi các tham số này qua các lần heartbeat để điều chỉnh hành vi Agent từ xa.

## 2. API Heartbeat và Trạng Thái

### 2.1 **POST /api/agent/heartbeat**

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

## 3. API Kết quả Lệnh

### 3.1 **POST /api/agent/command_result**

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
