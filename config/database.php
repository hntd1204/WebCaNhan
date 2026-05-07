<?php
// Cấu hình database dùng chung cho cả 2 dự án
// 1) Import file database_merged.sql vào MySQL/phpMyAdmin
// 2) Sửa các thông tin dưới đây theo hosting/local của bạn

$host = 'localhost';
$dbname = 'Random_quay';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Lỗi kết nối CSDL: ' . $e->getMessage());
}
