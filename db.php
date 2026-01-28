<?php
// db.php
// Tự động nhận diện môi trường để cấu hình DB
$is_local = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1');

if ($is_local) {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db_name = 'WebCaNhan';
} else {
    // Cấu hình cho Server thật (Hosting)
    $host = 'localhost'; // Hoặc IP hosting
    $user = 'user_hosting_cua_ban';
    $pass = 'pass_hosting_cua_ban';
    $db_name = 'ten_db_hosting';
}

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    // Trên môi trường thật, không nên hiện lỗi chi tiết ra màn hình
    die($is_local ? "Kết nối thất bại: " . $conn->connect_error : "Lỗi kết nối cơ sở dữ liệu. Vui lòng kiểm tra config.");
}
$conn->set_charset("utf8mb4");

// Khởi động session tại đây để dùng toàn cục
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}