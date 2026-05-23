-- ============================================================
--  setup.sql  —  Chạy file này trong phpMyAdmin hoặc MySQL CLI
--  mysql -u root -p < setup.sql
-- ============================================================

DROP DATABASE IF EXISTS cau_vong;
CREATE DATABASE IF NOT EXISTS cau_vong
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cau_vong;

-- ── Bảng tài khoản người dùng ────────────────────────────────
CREATE TABLE IF NOT EXISTS user_accounts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(150)  UNIQUE NOT NULL,
    password   VARCHAR(255)  NOT NULL,
    role       ENUM('admin','teacher','student','parent') NOT NULL DEFAULT 'student',
    status     ENUM('active','pending') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- ── Bảng Admins ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    address    VARCHAR(200) DEFAULT NULL,
    FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
);

-- ── Bảng Parents ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS parents (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    address    VARCHAR(200) DEFAULT NULL,
    FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
);

-- ── Bảng Teachers ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS teachers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    address    VARCHAR(200) DEFAULT NULL,
    subject    VARCHAR(50)  DEFAULT NULL,
    FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
);

-- ── Bảng Students ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    name       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)  DEFAULT NULL,
    address    VARCHAR(200) DEFAULT NULL,
    parent_id  INT          DEFAULT NULL,
    FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id)  REFERENCES parents(id) ON DELETE SET NULL
);

-- ── Bảng lớp học ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS classes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    subject     VARCHAR(50)  NOT NULL,
    teacher_id  INT          DEFAULT NULL,
    schedule    VARCHAR(100) DEFAULT NULL,
    total_slots INT          NOT NULL DEFAULT 15,
    enrolled          INT          NOT NULL DEFAULT 0,
    level             ENUM('Tiểu học','THCS','THPT') NOT NULL DEFAULT 'THPT',
    location          VARCHAR(50)  NOT NULL DEFAULT 'Online',
    description       TEXT         DEFAULT NULL,
    price_per_session INT          NOT NULL DEFAULT 150000,
    total_sessions    INT          NOT NULL DEFAULT 20,
    created_at        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
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
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id)   REFERENCES classes(id) ON DELETE CASCADE
);

-- ── Bảng điểm danh (Attendance) ────────────────────────────
CREATE TABLE IF NOT EXISTS attendance (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    class_id   INT NOT NULL,
    student_id INT NOT NULL,
    session_date DATE NOT NULL,
    status     ENUM('present','absent') NOT NULL DEFAULT 'present',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (class_id, student_id, session_date),
    FOREIGN KEY (class_id)   REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- ── Bảng thanh toán học phí (Payments) ─────────────────────
CREATE TABLE IF NOT EXISTS payments (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    student_id     INT NOT NULL,
    class_id       INT NOT NULL,
    amount         INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    note           TEXT DEFAULT NULL,
    status         ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    payment_date   DATE NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id)   REFERENCES classes(id) ON DELETE CASCADE
);
-- ── Bảng yêu cầu mở lớp (Class Requests) ─────────────────────
CREATE TABLE IF NOT EXISTS class_requests (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    requester_id   INT NOT NULL,
    subject        VARCHAR(50) NOT NULL,
    level          VARCHAR(50) NOT NULL,
    format         VARCHAR(50) NOT NULL,
    teacher_id     INT DEFAULT NULL,
    notes          TEXT,
    status         ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_reply    TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES user_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id)   REFERENCES teachers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS class_request_votes (
    request_id INT NOT NULL,
    account_id INT NOT NULL,
    PRIMARY KEY (request_id, account_id),
    FOREIGN KEY (request_id) REFERENCES class_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
);


-- ── Seed dữ liệu mẫu ────────────────────────────────────────
-- Mật khẩu đều là: 123456
INSERT INTO user_accounts (id, email, password, role, status) VALUES
(1, 'admin@rainbow.vn',   '123456', 'admin',   'active'),
(2, 'teacher@rainbow.vn', '123456', 'teacher', 'active'),
(3, 'student@rainbow.vn', '123456', 'student', 'active'),
(4, 'hoa@gmail.com',      '123456', 'teacher', 'active'),
(5, 'long@gmail.com',     '123456', 'teacher', 'active'),
(6, 'bao@gmail.com',      '123456', 'student', 'pending'),
(7, 'parent@rainbow.vn',  '123456', 'parent',  'active');

INSERT INTO admins (account_id, name, phone) VALUES
(1, 'Admin User', '0912000001');

INSERT INTO teachers (id, account_id, name, phone, subject) VALUES
(1, 2, 'Nguyễn Thị Lan', '0912000002', 'Toán'),
(2, 4, 'Phạm Thị Hoa',   '0912000004', 'Anh văn'),
(3, 5, 'Hoàng Đức Long', '0912000005', 'Vật lý');

INSERT INTO parents (id, account_id, name, phone) VALUES
(1, 7, 'Phụ Huynh Minh', '0912000007');

INSERT INTO students (id, account_id, name, phone, parent_id) VALUES
(1, 3, 'Trần Văn Minh', '0912000003', 1),
(2, 6, 'Lê Quốc Bảo',  '0912000006', 1);

INSERT INTO classes (name, subject, teacher_id, schedule, total_slots, enrolled, level, location, price_per_session, total_sessions) VALUES
('Toán 10 Cơ bản', 'Toán',    1, 'T2,T4,T6 19:00', 20, 15, 'THPT', 'Online', 150000, 24),
('Anh văn B1',     'Anh văn', 2, 'T3,T5 18:00',    15, 12, 'THCS', 'Online', 120000, 16),
('Vật lý 11',      'Vật lý',  3, 'T7 08:00',       10,  8, 'THPT', 'Trực tiếp', 180000, 12),
('Ngữ văn 9',      'Văn',     1, 'T2,T6 20:00',    12,  5, 'THCS', 'Online', 130000, 20);

INSERT INTO applications (name, email, subject, interview_date, interview_time, status) VALUES
('Trần Thị Anh',    'anh@gmail.com',   'Toán',    '2026-05-07', '09:00', 'pending'),
('Lê Văn Cường',    'cuong@gmail.com', 'Anh văn', '2026-05-07', '14:00', 'interview'),
('Nguyễn Minh Dũng','dung@gmail.com',  'Vật lý',  '2026-05-05', '10:00', 'approved'),
('Hoàng Văn Chung', 'chung@gmail.com', 'Hóa học', NULL,         NULL,    'pending');

-- Seed một vài dữ liệu điểm danh và thanh toán
INSERT INTO enrollments (student_id, class_id) VALUES
(1, 1),
(2, 2);

INSERT INTO attendance (class_id, student_id, session_date, status) VALUES
(1, 1, '2025-05-01', 'present'),
(1, 1, '2025-05-03', 'present'),
(1, 1, '2025-05-05', 'absent'),
(1, 1, '2025-05-08', 'present'),
(2, 2, '2025-05-02', 'present'),
(2, 2, '2025-05-04', 'present');

INSERT INTO payments (student_id, class_id, amount, payment_method, note, status, payment_date) VALUES
(1, 1, 300000, 'Chuyển khoản', 'Thanh toán đợt 1', 'verified', '2025-05-10'),
(2, 2, 240000, 'Tiền mặt', 'Đóng trực tiếp', 'verified', '2025-05-11');
