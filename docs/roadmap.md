# Roadmap phát triển UniLab

## Phân loại ưu tiên theo MoSCoW

- **M (Must have)**: Chức năng thiết yếu, không thể thiếu
- **S (Should have)**: Quan trọng nhưng không khẩn cấp
- **C (Could have)**: Mong muốn nhưng có thể bỏ qua
- **W (Won't have)**: Sẽ được xem xét trong tương lai

## 1. Core Infrastructure - Hạ tầng cốt lõi (M)

- [x] Thiết lập Database Schema (migrations, models, relationships)
- [x] Cấu hình Laravel Server cơ bản
- [x] Thiết lập RabbitMQ và Message Queue architecture
- [x] Cài đặt Laravel Sanctum cho API authentication
- [ ] Security framework cho API endpoints

## 2. Backend Core - Chức năng backend chính (M)

- [x] API CRUD cho phòng và máy tính
- [ ] Hệ thống sinh secret key bảo mật
- [x] Endpoint POST `/api/commands` và job xử lý
- [ ] API tạo Installation Scripts
- [ ] Endpoint đăng ký Agent (`/api/agent/register`)
- [ ] Endpoint xác thực Agent (`/api/agent/token`)
- [ ] Endpoint heartbeat (`/api/agent/heartbeat`)
- [x] API nhận kết quả lệnh từ Agent

## 3. Agent Development - Phát triển Agent (M)

- [x] Module lắng nghe lệnh từ RabbitMQ
- [ ] Module thực thi các lệnh cơ bản (SHUTDOWN, RESTART)
- [ ] Module gửi heartbeat và thông tin tài nguyên
- [ ] Module tự động cập nhật Agent
- [ ] Cơ chế phục hồi khi mất kết nối
- [ ] Xử lý token authentication

## 4. Frontend Dashboard Core - Dashboard cơ bản (M)

- [x] Thiết kế hệ thống UI components sử dụng Vue.js
- [x] Xây dựng layout chính và navigation
- [x] Tích hợp authentication UI
- [x] Giao diện quản lý phòng và máy tính
- [x] Giao diện tạo và gửi lệnh đến agent
- [ ] UI hiển thị trạng thái máy tính
- [ ] Responsive design cho tất cả màn hình

## 5. Command & Control - Hệ thống điều khiển (M)

- [x] Thiết lập RabbitMQ exchanges và routing patterns
- [ ] Command dispatcher từ server đến agent
- [ ] Triển khai các loại lệnh cơ bản (SHUTDOWN, RESTART)
- [ ] Tracking và hiển thị trạng thái lệnh
- [ ] Hệ thống queuing commands khi agent offline
- [ ] Xử lý retry cho các lệnh thất bại

## 6. Agent Auto-Update - Cập nhật tự động (M)

- [ ] Versioning system cho Agent
- [ ] API endpoint cung cấp metadata về phiên bản mới
- [ ] Module download và verify package cập nhật
- [ ] Cơ chế cài đặt cập nhật và rollback nếu lỗi
- [ ] Hệ thống phân phối cập nhật theo nhóm/phòng

## 7. User Management - Quản lý người dùng (S)

- [ ] Hệ thống phân quyền (RBAC)
- [ ] Quản lý người dùng và nhóm quyền
- [ ] Nhật ký hoạt động người dùng
- [ ] Self-service password reset
- [ ] Profile và cài đặt người dùng

## 8. Advanced Monitoring - Giám sát nâng cao (S)

- [ ] Dashboard giám sát real-time
- [ ] Biểu đồ theo dõi hiệu suất
- [ ] Hệ thống cảnh báo khi máy offline/quá tải
- [ ] Monitoring API thời gian thực
- [ ] Trang trạng thái hệ thống (health monitoring)

## 9. Advanced Commands - Lệnh nâng cao (S)

- [ ] Lệnh INSTALL cho cài đặt phần mềm
- [ ] Lệnh UPDATE cho cập nhật hệ thống/phần mềm
- [ ] Lệnh EXECUTE cho thực thi script tùy chỉnh
- [ ] Job scheduling cho lệnh định kỳ
- [ ] Lệnh template và kịch bản tùy chỉnh

## 10. Deployment Tools - Công cụ triển khai (S)

- [ ] Tạo installation scripts hàng loạt
- [ ] Công cụ triển khai từ xa (Group Policy/Ansible)
- [ ] Giao diện tạo và tải scripts từ Dashboard
- [ ] Package management cho Agent
- [ ] Deployment analytics và báo cáo

## 11. Data Management - Quản lý dữ liệu (C)

- [ ] Tính năng import/export danh sách máy
- [ ] Kéo-thả để sắp xếp vị trí máy tính trong phòng
- [ ] Quản lý metadata và custom fields
- [ ] Tagging và phân loại máy tính
- [ ] Data cleansing và validation tools

## 12. Reporting - Báo cáo và phân tích (C)

- [ ] Trang tạo và xem báo cáo sử dụng phòng máy
- [ ] Biểu đồ và thống kê sử dụng tài nguyên
- [ ] Báo cáo hiệu suất và tình trạng phần cứng
- [ ] Export báo cáo (PDF, Excel)
- [ ] Custom dashboard widgets

## 13. Testing & Quality - Kiểm thử & Chất lượng (S)

- [ ] Unit tests cho API endpoints
- [ ] Integration tests cho luồng xử lý lệnh
- [ ] Tests cho Agent trong các môi trường khác nhau
- [ ] Security tests cho hệ thống xác thực
- [ ] Performance tests cho heartbeat và message queue
- [ ] Chaos testing và recovery testing

## 14. Documentation - Tài liệu hướng dẫn (S)

- [ ] API documentation đầy đủ
- [ ] Tài liệu hướng dẫn sử dụng cho admin
- [ ] Tài liệu cài đặt và cấu hình hệ thống
- [ ] Video tutorials cho các tính năng chính
- [ ] Tài liệu troubleshooting và FAQ

## 15. Production Release - Phát hành (C)

- [ ] Chuẩn bị môi trường production
- [ ] Thiết lập CI/CD pipeline
- [ ] Triển khai phiên bản beta và thu thập feedback
- [ ] Sửa lỗi và cải tiến dựa trên feedback
- [ ] Release phiên bản stable 1.0
- [ ] Kế hoạch bảo trì và nâng cấp

## 16. Advanced Features - Tính năng nâng cao (W)

- [ ] API mở rộng cho tích hợp bên thứ ba
- [ ] Remote desktop/control qua web browser
- [ ] Mobile app cho giám sát từ xa
- [ ] Automatic provisioning và zero-touch deployment
- [ ] AI/ML cho phát hiện bất thường và dự đoán lỗi
