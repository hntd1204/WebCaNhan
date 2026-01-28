<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $store = $stmt->get_result()->fetch_assoc();

    if ($store && password_verify($password, $store['password'])) {
        $_SESSION['user_id'] = $store['id'];
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - My Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .login-box {
        width: 100%;
        max-width: 400px;
        padding: 2rem;
    }
    </style>
</head>

<body>
    <div class="glass-panel login-box text-center">
        <h2 class="mb-4 fw-bold" style="color: #ffb7b2;">Welcome Back! 🌸</h2>
        <?php if ($error): ?>
        <div class="alert alert-danger p-2"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label text-muted">Username</label>
                <input type="text" name="username" class="form-control rounded-pill" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label text-muted">Password</label>
                <input type="password" name="password" class="form-control rounded-pill" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 rounded-pill mt-2">Đăng nhập</button>
        </form>
    </div>
</body>

</html>