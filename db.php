<?php
// Cấu hình Database
$host = 'localhost';
$user = 'root';      // Điền username host (thường là user_hosting)
$pass = '';          // Điền password
$db_name = 'WebCaNhan'; // Tên database bạn vừa tạo

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");