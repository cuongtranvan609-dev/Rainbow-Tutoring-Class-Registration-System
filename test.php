<?php
require 'config/db.php';
$stmt = $pdo->query('SELECT * FROM applications');
print_r($stmt->fetchAll());
