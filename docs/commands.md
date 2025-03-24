# Các loại lệnh và xử lý lỗi trong UniLab

## 1. Các loại lệnh hỗ trợ

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

## 2. Xử lý lỗi và phục hồi

1. **Mất kết nối**:

    - Agent sẽ lưu cache lệnh chưa hoàn thành
    - Tự động kết nối lại sau 30 giây và tiếp tục thực thi

2. **Lệnh thất bại**:

    - Thử lại tối đa 3 lần với các lệnh quan trọng
    - Ghi log chi tiết lỗi và gửi về server

3. **Token hết hạn**:
    - Tự động làm mới token khi gặp lỗi 401 Unauthorized
    - Quay lại quy trình đăng ký nếu không thể làm mới token
