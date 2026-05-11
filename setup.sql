-- ============================================================
--  setup.sql  —  Chạy file này trong phpMyAdmin hoặc MySQL CLI
--  mysql -u root -p < setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS cau_vong
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cau_vong;

-- ── Bảng người dùng ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  UNIQUE NOT NULL,
    password   VARCHAR(255)  NOT NULL,
    role       ENUM('admin','teacher','student') NOT NULL DEFAULT 'student',
    status     ENUM('active','pending') NOT NULL DEFAULT 'pending',
    phone      VARCHAR(20)   DEFAULT NULL,
    address    VARCHAR(200)  DEFAULT NULL,
    subject    VARCHAR(50)   DEFAULT NULL,   -- dành cho teacher
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- ── Bảng lớp học ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS classes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    subject     VARCHAR(50)  NOT NULL,
    teacher_id  INT          DEFAULT NULL,
    schedule    VARCHAR(100) DEFAULT NULL,
    total_slots INT          NOT NULL DEFAULT 15,
    enrolled    INT          NOT NULL DEFAULT 0,
    level       ENUM('Tiểu học','THCS','THPT') NOT NULL DEFAULT 'THPT',
    location    VARCHAR(50)  NOT NULL DEFAULT 'Online',
    description TEXT         DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ── Bảng hồ sơ ứng tuyển ────────────────────────────────────
CREATE TABLE IF NOT EXISTS applications (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL,
    phone          VARCHAR(20)  DEFAULT NULL,
    subject        VARCHAR(50)  NOT NULL,
    education      VARCHAR(50)  DEFAULT NULL,
    bio            TEXT         DEFAULT NULL,
    cv_file        VARCHAR(255) DEFAULT NULL,
    interview_date DATE         DEFAULT NULL,
    interview_time VARCHAR(20)  DEFAULT NULL,
    interview_mode VARCHAR(50)  DEFAULT 'Online (Google Meet)',
    status         ENUM('pending','interview','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ── Bảng đăng ký lớp (học sinh ↔ lớp) ──────────────────────
CREATE TABLE IF NOT EXISTS enrollments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id   INT NOT NULL,
    joined_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (student_id, class_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id)   REFERENCES classes(id) ON DELETE CASCADE
);

-- ── Seed dữ liệu mẫu ────────────────────────────────────────
-- Mật khẩu đều là: 123456
INSERT INTO users (name, email, password, role, status, phone) VALUES
('Admin User',      'admin@rainbow.vn',   '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'admin',   'active', '0912000001'),
('Nguyễn Thị Lan',  'teacher@rainbow.vn', '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'teacher', 'active', '0912000002'),
('Trần Văn Minh',   'student@rainbow.vn', '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'student', 'active', '0912000003'),
('Phạm Thị Hoa',    'hoa@gmail.com',      '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'teacher', 'active', '0912000004'),
('Hoàng Đức Long',  'long@gmail.com',     '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'teacher', 'active', '0912000005'),
('Lê Quốc Bảo',     'bao@gmail.com',      '$2y$10$TKh8H1.PfuNSn9QkjUOGGuVnTuFT1AUBEsGFCEkmFvtpK5/S0AHn2', 'student', 'pending','0912000006');

INSERT INTO classes (name, subject, teacher_id, schedule, total_slots, enrolled, level, location) VALUES
('Toán 10 Cơ bản', 'Toán',    2, 'T2,T4,T6 19:00', 20, 15, 'THPT', 'Online'),
('Anh văn B1',     'Anh văn', 4, 'T3,T5 18:00',    15, 12, 'THCS', 'Online'),
('Vật lý 11',      'Vật lý',  5, 'T7 08:00',       10,  8, 'THPT', 'Trực tiếp'),
('Ngữ văn 9',      'Văn',     2, 'T2,T6 20:00',    12,  5, 'THCS', 'Online');

INSERT INTO applications (name, email, subject, interview_date, interview_time, status) VALUES
('Trần Thị Anh',    'anh@gmail.com',   'Toán',    '2026-05-07', '09:00', 'pending'),
('Lê Văn Cường',    'cuong@gmail.com', 'Anh văn', '2026-05-07', '14:00', 'interview'),
('Nguyễn Minh Dũng','dung@gmail.com',  'Vật lý',  '2026-05-05', '10:00', 'approved'),
('Hoàng Văn Chung', 'chung@gmail.com', 'Hóa học', NULL,         NULL,    'pending');
