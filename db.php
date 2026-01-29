<?php
$host = 'localhost';
$dbname = 'my_places_db';
$username = 'root'; // Thay bằng user của bạn
$password = ''; // Thay bằng pass của bạn

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}