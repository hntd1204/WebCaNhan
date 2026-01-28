<?php
require_once 'db.php'; // Kết nối CSDL

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $key = $_POST['secret_key'];

    // Mật khẩu bảo vệ chính file này (để người lạ không vào tạo bừa)
    // Bạn cứ để nguyên hoặc đổi nếu thích
    if ($key != '123456') {
        $msg = "<div class='alert alert-danger'>Sai mã bảo mật!</div>";
    } elseif (empty($username) || empty($password)) {
        $msg = "<div class='alert alert-warning'>Vui lòng điền đủ thông tin!</div>";
    } else {
        // 1. Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 2. Kiểm tra xem user đã tồn tại chưa
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $msg = "<div class='alert alert-warning'>Tài khoản '$username' đã tồn tại! Hãy chọn tên khác.</div>";
        } else {
            // 3. Thêm vào Database
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>
                            ✅ Đã tạo tài khoản <b>$username</b> thành công!<br>
                            👉 <a href='login.php'>Bấm vào đây để Đăng nhập</a><br>
                            ⚠️ <b>LƯU Ý QUAN TRỌNG:</b> Hãy xóa file <code>setup_user.php</code> này ngay sau khi tạo xong để bảo mật.
                        </div>";
            } else {
                $msg = "<div class='alert alert-danger'>Lỗi Database: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Tài Khoản Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4 text-primary">Tạo Tài Khoản Mới</h3>

        <?= $msg ?>

        <?php if (strpos($msg, 'success') === false): ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập muốn tạo</label>
                <input type="text" name="username" class="form-control" placeholder="Ví dụ: myadmin" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="text" name="password" class="form-control" placeholder="Nhập mật khẩu của bạn" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mã bảo mật (Mặc định: 123456)</label>
                <input type="password" name="secret_key" class="form-control" value="123456">
                <div class="form-text small">Để ngăn người lạ dùng file này.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Tạo ngay</button>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>