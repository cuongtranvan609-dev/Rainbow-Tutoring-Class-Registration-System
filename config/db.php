<?php
// ============================================================
//  config/db.php  —  Kết nối PDO MySQL
//  Chỉnh DB_USER / DB_PASS nếu bạn dùng WAMP có mật khẩu
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'cau_vong');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP mặc định để trống

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Không kết nối được database: ' . $e->getMessage()]));
}
