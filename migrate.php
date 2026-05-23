<?php
require_once 'config/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS class_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            requester_id INT NOT NULL,
            subject VARCHAR(50) NOT NULL,
            level VARCHAR(50) NOT NULL,
            format VARCHAR(50) NOT NULL,
            teacher_id INT DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            admin_reply TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (requester_id) REFERENCES user_accounts(id) ON DELETE CASCADE,
            FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
        );
        
        CREATE TABLE IF NOT EXISTS class_request_votes (
            request_id INT NOT NULL,
            account_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (request_id, account_id),
            FOREIGN KEY (request_id) REFERENCES class_requests(id) ON DELETE CASCADE,
            FOREIGN KEY (account_id) REFERENCES user_accounts(id) ON DELETE CASCADE
        );
    ");
    echo "Migration successful!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
