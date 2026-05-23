# 🌈 Lớp Gia Sư Cầu Vồng — PHP + MySQL

## Cấu trúc thư mục

```
cauVong/
├── index.php               ← Trang chủ (entry point)
├── script.js               ← Toàn bộ JS (đã đổi sang fetch API thật)
├── style.css               ← CSS giữ nguyên từ bản gốc
├── setup.sql               ← SQL tạo database + seed data
├── config/
│   └── db.php              ← Kết nối PDO MySQL
├── api/
│   ├── auth.php            ← Login / Logout / Register / Me / Update profile
│   ├── classes.php         ← CRUD lớp học
│   ├── users.php           ← CRUD người dùng (admin)
│   ├── applications.php    ← CRUD hồ sơ ứng tuyển
│   └── enrollments.php     ← Đăng ký / Hủy lớp học
└── uploads/                ← Thư mục chứa CV upload
```

---

## Cài đặt (5 bước)

### Bước 1 — Copy vào htdocs
```
XAMPP: C:\xampp\htdocs\cauVong\
WAMP:  C:\wamp64\www\cauVong\
```

### Bước 2 — Tạo database
Mở **phpMyAdmin** → tab **SQL** → paste nội dung `setup.sql` → chạy

Hoặc dùng terminal:
```bash
mysql -u root -p < setup.sql
```

### Bước 3 — Kiểm tra config/db.php
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cau_vong');
define('DB_USER', 'root');
define('DB_PASS', '');   // WAMP có password thì thêm vào đây
```

### Bước 4 — Phân quyền thư mục uploads
```
Windows: thư mục uploads/ cần có quyền ghi (thường tự động OK)
Linux:   chmod 775 uploads/
```

### Bước 5 — Truy cập
```
http://localhost/cauVong/
```

---

## Tài khoản demo (mật khẩu đều là: `123456`)

| Email                  | Vai trò   |
|------------------------|-----------|
| admin@rainbow.vn       | Admin     |
| teacher@rainbow.vn     | Giáo viên |
| student@rainbow.vn     | Học sinh  |

---

## API Endpoints

| Endpoint               | Methods          | Mô tả                              |
|------------------------|------------------|------------------------------------|
| `api/auth.php`         | POST             | login / logout / register / me     |
| `api/classes.php`      | GET POST PUT DEL | CRUD lớp học                       |
| `api/users.php`        | GET POST PUT DEL | CRUD người dùng (admin only)       |
| `api/applications.php` | GET POST PUT DEL | CRUD hồ sơ ứng tuyển              |
| `api/enrollments.php`  | GET POST DEL     | Đăng ký / hủy lớp học             |

---

## Lưu ý bảo mật
- Mật khẩu lưu bằng `password_hash()` (bcrypt) — **không bao giờ lưu plaintext**
- Tất cả query dùng **PDO Prepared Statements** — chống SQL injection
- Kiểm tra session trước mọi thao tác write
- File upload: validate extension + size, lưu tên ngẫu nhiên
- Production: thêm CSRF token, rate limiting, HTTPS
