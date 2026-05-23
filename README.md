# Nền tảng Gia sư Cầu Vồng 🌈

Dự án **Lớp Gia sư Cầu Vồng** là một hệ thống web ứng dụng (Web Application) quản lý trung tâm gia sư và lớp học trực tuyến. Hệ thống cung cấp một nền tảng kết nối trực tiếp giữa **Ban quản trị (Admin)**, **Giáo viên**, **Học sinh** và **Phụ huynh**.

## 🌟 Tính năng nổi bật

### 1. Hệ thống Đa quyền (Multi-role System)
Hệ thống hỗ trợ 4 phân quyền riêng biệt với các chức năng khác nhau:
- **Admin (Quản trị viên):** Quản lý toàn bộ người dùng, duyệt giáo viên, tạo lớp học, duyệt yêu cầu mở lớp, và quản lý doanh thu/học phí.
- **Giáo viên:** Cập nhật thông tin hồ sơ, quản lý các lớp học đang giảng dạy, xem danh sách học sinh.
- **Học sinh:** Tìm kiếm lớp học, đăng ký tham gia lớp học, xem lịch học và đóng học phí.
- **Phụ huynh:** Theo dõi tình hình học tập của con em, đóng học phí, và có quyền **yêu cầu mở lớp học mới**.

### 2. Tính năng Crowdsourcing Mở Lớp (Nổi bật) 🔥
- Phụ huynh/Học sinh có thể chủ động **Yêu cầu mở lớp mới** nếu trung tâm chưa có lớp phù hợp (Ví dụ: "Lớp Tiếng Anh giao tiếp cấp 1").
- Người dùng khác có thể vào xem danh sách yêu cầu và bấm **Vote (Tôi cũng muốn học)** để tăng hạng cho yêu cầu đó.
- Khi nhận thấy nhu cầu đủ lớn, **Admin** có thể duyệt yêu cầu, gán giáo viên và tự động chuyển đổi yêu cầu đó thành một **Lớp học chính thức**.

### 3. Giao diện (UI/UX) Hiện đại
- Tối ưu hóa cho thiết bị di động (Mobile Responsive).
- Áp dụng các hiệu ứng animation (hiệu ứng mượt mà), modal popups, và thiết kế Accordion (đóng mở thông minh) giúp tiết kiệm diện tích.
- Giao diện thân thiện, sử dụng tông màu pastel (xanh mint, cam nhạt) tạo cảm giác an tâm và chuyên nghiệp.

### 4. Quản lý Đăng nhập Đơn giản hóa
- Hỗ trợ đăng nhập nhanh bằng số điện thoại/email.
- (Môi trường Dev): Hệ thống đang sử dụng định dạng mật khẩu số (VD: `123456`) để tối ưu hóa trải nghiệm kiểm thử và sử dụng cho mọi đối tượng.

## 🛠 Công nghệ sử dụng
- **Frontend:** HTML5, CSS3 thuần (Vanilla CSS tự xây dựng UI framework nội bộ), JavaScript (Vanilla JS thao tác DOM).
- **Backend:** PHP 8.x (sử dụng PDO để chống SQL Injection).
- **Database:** MySQL.
- **Môi trường Server:** XAMPP / Apache.

## 🚀 Hướng dẫn cài đặt (Localhost)

1. Cài đặt **XAMPP** và bật 2 module: **Apache** và **MySQL**.
2. Clone/Copy toàn bộ mã nguồn dự án vào thư mục `C:\xampp\htdocs\rainbow`.
3. Mở trình duyệt, truy cập `http://localhost/phpmyadmin`.
4. Tạo một Database mới có tên là `cau_vong` (Collation: `utf8mb4_unicode_ci`).
5. Import file `setup.sql` có sẵn trong thư mục gốc để tự động tạo các bảng và dữ liệu mẫu (Seed data).
6. Truy cập `http://localhost/rainbow` để trải nghiệm dự án.

## 🔑 Tài khoản Demo (Mặc định)

Sau khi cài đặt xong, bạn có thể sử dụng các tài khoản sau để đăng nhập (Mật khẩu chung cho tất cả là: `123456`):

- **Admin:** `admin@rainbow.vn`
- **Giáo viên:** `teacher@rainbow.vn`
- **Học sinh:** `student@rainbow.vn`
- **Phụ huynh:** `parent@rainbow.vn`

---
*Dự án được xây dựng và phát triển để đem lại trải nghiệm giáo dục tốt nhất.*
