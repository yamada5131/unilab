# Agent Installation Scripts

## 1. Tổng quan về Installation Scripts

Thay vì sử dụng installer truyền thống, hệ thống tạo script cài đặt tự động cho từng máy tính. Script này sẽ thực hiện việc tải, cài đặt và cấu hình Agent để kết nối với server.

## 2. Quy trình tạo Installation Scripts

1. **Tạo Script Cá nhân hóa**:

    - Admin chọn máy tính cần cài đặt Agent trong Dashboard
    - Hệ thống sinh script cài đặt (PowerShell/Bash) riêng cho máy tính đó
    - Script được tạo với các thông số nhận dạng máy tính đã được định nghĩa trước

2. **Nội dung Script**:
    - Tải xuống phần mềm Agent từ repository
    - Tạo file cấu hình với thông tin đặc thù cho máy tính
    - Cài đặt Agent và đăng ký nó như service hệ thống
    - Khởi động service và xác minh kết nối với server

## 3. Ví dụ về Installation Script

**Python Script (Windows):**

```python
#!/usr/bin/env python3
# UniLab Agent Installation Script
# Auto-generated for Computer: LAB-PC-42 in Room: A1-404
# Generated on: 2025-03-08

import os
import sys
import json
import time
import shutil
import zipfile
import tempfile
import subprocess
import urllib.request
from pathlib import Path

# Thông tin cài đặt
SERVER_URL = "https://unilab.example.com"
SECRET_KEY = "sk_wAb5DcE2fG3hI4jK5"
AGENT_DOWNLOAD_URL = "https://unilab.example.com/downloads/agent/latest/windows"
CONFIG_DIR = os.path.join(os.environ.get('PROGRAMDATA', 'C:\\ProgramData'), 'UniLab', 'Agent')
INSTALL_DIR = os.path.join(os.environ.get('PROGRAMFILES', 'C:\\Program Files'), 'UniLab', 'Agent')

def print_status(message, status="info"):
    """In thông báo với màu sắc"""
    colors = {
        "info": "\033[94m",    # Blue
        "success": "\033[92m",  # Green
        "warning": "\033[93m",  # Yellow
        "error": "\033[91m",    # Red
        "end": "\033[0m"        # Reset
    }
    
    print(f"{colors.get(status, '')}{message}{colors['end']}")

def create_directories():
    """Tạo thư mục cài đặt và cấu hình"""
    print_status("Tạo thư mục cài đặt...", "info")
    
    os.makedirs(CONFIG_DIR, exist_ok=True)
    os.makedirs(INSTALL_DIR, exist_ok=True)
    
    return True

def download_agent():
    """Tải agent từ server"""
    print_status("Đang tải phần mềm Agent...", "info")
    
    try:
        temp_file = os.path.join(tempfile.gettempdir(), "unilab_agent.zip")
        urllib.request.urlretrieve(AGENT_DOWNLOAD_URL, temp_file)
        
        # Giải nén tập tin
        with zipfile.ZipFile(temp_file, 'r') as zip_ref:
            zip_ref.extractall(INSTALL_DIR)
        
        os.remove(temp_file)
        return True
    except Exception as e:
        print_status(f"Lỗi khi tải agent: {str(e)}", "error")
        return False

def create_config():
    """Tạo file cấu hình"""
    print_status("Tạo file cấu hình...", "info")
    
    config = {
        "server": {
            "url": SERVER_URL,
            "api_base": "/api/agent"
        },
        "agent": {
            "heartbeat_interval": 300,
            "logging_level": "info"
        },
        "security": {
            "secret_key": SECRET_KEY
        },
        "identification": {
            "computer_id": "550e8400-e29b-41d4-a716-446655440000",
            "mac_address": "00:1B:44:11:3A:B7", 
            "room_id": "123",
            "location": "R3-S5",
            "expected_hostname": "LAB-PC-42"
        }
    }
    
    config_path = os.path.join(CONFIG_DIR, "config.json")
    with open(config_path, 'w', encoding='utf-8') as f:
        json.dump(config, f, indent=4)
    
    return True

def install_service():
    """Cài đặt agent như một Windows service"""
    print_status("Cài đặt Windows Service...", "info")
    
    try:
        service_path = os.path.join(INSTALL_DIR, "install-service.exe")
        subprocess.run([service_path], check=True)
        return True
    except Exception as e:
        print_status(f"Lỗi khi cài đặt service: {str(e)}", "error")
        return False

def start_service():
    """Khởi động service"""
    print_status("Khởi động UniLab Agent service...", "info")
    
    try:
        if sys.platform == 'win32':
            subprocess.run(["sc", "start", "UniLabAgent"], check=True)
        else:
            subprocess.run(["systemctl", "start", "unilab-agent"], check=True)
        return True
    except Exception as e:
        print_status(f"Lỗi khi khởi động service: {str(e)}", "error")
        return False

def verify_installation():
    """Kiểm tra trạng thái cài đặt"""
    print_status("Xác minh cài đặt...", "info")
    
    try:
        if sys.platform == 'win32':
            result = subprocess.run(
                ["sc", "query", "UniLabAgent"], 
                capture_output=True, 
                text=True, 
                check=True
            )
            running = "RUNNING" in result.stdout
        else:
            result = subprocess.run(
                ["systemctl", "is-active", "unilab-agent"],
                capture_output=True,
                text=True
            )
            running = "active" in result.stdout
            
        if running:
            print_status("Cài đặt hoàn tất thành công!", "success")
        else:
            print_status("Cài đặt hoàn tất, nhưng service không chạy. Vui lòng kiểm tra logs.", "warning")
        
        return running
    except Exception as e:
        print_status(f"Lỗi khi kiểm tra service: {str(e)}", "error")
        return False

def main():
    """Quy trình cài đặt chính"""
    print_status("=== BẮT ĐẦU CÀI ĐẶT UNILAB AGENT ===", "success")
    
    # Kiểm tra quyền admin
    if sys.platform == 'win32' and os.name == 'nt':
        try:
            is_admin = os.getuid() == 0
        except AttributeError:
            import ctypes
            is_admin = ctypes.windll.shell32.IsUserAnAdmin() != 0
            
        if not is_admin:
            print_status("Script cần được chạy với quyền Administrator!", "error")
            return False
    
    # Các bước cài đặt
    steps = [
        ("Tạo thư mục", create_directories),
        ("Tải phần mềm Agent", download_agent),
        ("Tạo file cấu hình", create_config),
        ("Cài đặt service", install_service),
        ("Khởi động service", start_service),
        ("Xác minh cài đặt", verify_installation)
    ]
    
    for step_name, step_func in steps:
        print_status(f"\nBước: {step_name}", "info")
        success = step_func()
        if not success:
            print_status(f"Cài đặt thất bại ở bước: {step_name}", "error")
            return False
            
    print_status("\n=== CÀI ĐẶT UNILAB AGENT HOÀN TẤT ===", "success")
    print_status("Agent đã được cài đặt và đang chạy.", "success")
    print_status("Máy tính sẽ tự động đăng ký với UniLab Server trong vài phút.", "info")
    return True

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
```

## 4. Ưu điểm khi sử dụng Python cho Installation Script
1. Tính nhất quán - Sử dụng Python cho cả Agent và Installation Script
2. Khả năng đa nền tảng - Mã nguồn Python có thể chạy trên nhiều hệ điều hành với ít thay đổi
3. Dễ tùy biến - Có thể dễ dàng thêm tính năng mới
4. Xử lý lỗi tốt hơn - Python có hệ thống xử lý ngoại lệ mạnh mẽ
5. Hỗ trợ thư viện phong phú - Sử dụng các thư viện Python có sẵn cho nhiều tác vụ
6. Trình bày UI tốt hơn - Dễ dàng thêm màu sắc, thanh tiến trình, v.v.

### Quy trình sử dụng

1. **Tải script**: Admin tải script từ Dashboard
2. **Chạy script**: Admin chạy script trên máy tính đích với quyền admin
3. **Xác minh**: Script tự động cài đặt, cấu hình và khởi động Agent
4. **Đăng ký**: Agent khi khởi động sẽ tự động đăng ký với server

## 5. Triển khai với Ansible

Hệ thống hỗ trợ cài đặt Agent trên nhiều máy tính cùng lúc mà không cần admin phải đi từng máy:

```yaml
# playbook.yml
- hosts: lab_computers
  become: yes
  tasks:
    - name: Tạo thư mục tạm thời
      file:
        path: /tmp/unilab-agent
        state: directory
        mode: '0755'
      
    - name: Sao chép installation script
      copy:
        src: "files/install-agent-{{ inventory_hostname }}.py"
        dest: /tmp/unilab-agent/install.py
        mode: '0755'
      
    - name: Chạy installation script
      command: python3 /tmp/unilab-agent/install.py
      register: install_result
      
    - name: Xem log cài đặt
      debug:
        var: install_result.stdout_lines
```

## 6. Cấu trúc thông tin định danh

Mỗi script cài đặt chứa thông tin định danh riêng cho từng máy tính:

```json
// Cấu trúc identification trong config.json
{
    "identification": {
        "computer_id": "550e8400-e29b-41d4-a716-446655440000",
        "mac_address": "00:1B:44:11:3A:B7",
        "room_id": "123",
        "location": "R3-S5",
        "expected_hostname": "LAB-PC-42"
    },
    "security": {
        "secret_key": "sk_wAb5DcE2fG3hI4jK5"
    }
}
```

## 7. Về Secret Key

Secret key đóng vai trò quan trọng trong quy trình xác thực Agent:

1. **Mục đích**:

    - Cơ chế xác thực một lần trong quá trình đăng ký ban đầu
    - Ngăn chặn việc máy tính không xác định tự đăng ký vào hệ thống
    - Đóng vai trò "mật khẩu tạm thời" trước khi token hệ thống được cấp

2. **Lưu trữ**:

    - **Database**: Lưu dạng hash cùng với bản ghi máy tính
    - **Script**: Nhúng dạng plaintext để sử dụng khi cài đặt
    - **Agent**: Lưu tạm thời trong quá trình đăng ký, xóa sau khi nhận token

3. **Sinh và xác thực**:

    ```php
    // Sinh secret key (Laravel)
    $secretKey = 'sk_' . Str::random(16);
    $computer->secret_key = Hash::make($secretKey);

    // Xác thực secret key (Laravel)
    if (!Hash::check($request->secret_key, $computer->secret_key)) {
        return response()->json(['error' => 'Invalid secret key'], 401);
    }
    ```

4. **Vòng đời**:
    - Được tạo khi admin thêm máy tính vào hệ thống
    - Sử dụng trong quá trình đăng ký Agent
    - Bị vô hiệu hóa sau lần sử dụng đầu tiên thành công
    - Có thể tạo lại nếu cần thiết (ví dụ: cài đặt lại Agent)
