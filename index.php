<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website Huỳnh Nguyễn Thành Đạt</title>
  <style>
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: #f4f7fb;
      color: #111827
    }

    .nav {
      position: sticky;
      top: 0;
      background: #111827;
      color: white;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
      z-index: 10
    }

    .brand {
      font-weight: 800;
      font-size: 18px
    }

    .menu {
      display: flex;
      gap: 10px;
      flex-wrap: wrap
    }

    .menu a {
      color: white;
      text-decoration: none;
      background: rgba(255, 255, 255, .1);
      padding: 10px 14px;
      border-radius: 999px;
      font-weight: 700
    }

    .menu a:hover {
      background: #2563eb
    }

    .hero {
      max-width: 1000px;
      margin: 60px auto 30px;
      padding: 0 20px;
      text-align: center
    }

    .hero h1 {
      font-size: 42px;
      margin: 0 0 12px
    }

    .hero p {
      color: #475569;
      font-size: 18px
    }

    .cards {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px
    }

    .card {
      background: white;
      border-radius: 24px;
      padding: 28px;
      box-shadow: 0 12px 35px rgba(15, 23, 42, .08);
      border: 1px solid #e5e7eb
    }

    .card h2 {
      margin-top: 0
    }

    .card p {
      color: #64748b;
      line-height: 1.6
    }

    .btn {
      display: inline-block;
      margin-top: 12px;
      background: #2563eb;
      color: white;
      text-decoration: none;
      padding: 12px 18px;
      border-radius: 12px;
      font-weight: 800
    }

    .btn.alt {
      background: #7c3aed
    }

    .note {
      max-width: 1000px;
      margin: 30px auto;
      padding: 16px 20px;
      color: #475569;
      background: #fff;
      border: 1px dashed #cbd5e1;
      border-radius: 16px
    }
  </style>
</head>

<body>
  <nav class="nav">
    <div class="brand">Website Huỳnh Nguyễn Thành Đạt</div>
    <div class="menu">
      <a href="index.php">Trang chủ</a>
      <a href="webcanhan/index.php">Web save địa điểm</a>
      <a href="random-quay/index.php">Random Quay</a>
    </div>
  </nav>
  <section class="hero">
    <h1>Chọn dự án muốn sử dụng</h1>
  </section>
  <main class="cards">
    <article class="card">
      <h2>Web save địa điểm</h2>
      <p>Quản lý thành phố, quận/huyện, danh mục và địa điểm.</p>
      <a class="btn" href="webcanhan/index.php">Vào Web save địa điểm</a>
    </article>
    <article class="card">
      <h2>Random Quay</h2>
      <p>Vòng quay, mini game, tài khoản, nhiệm vụ, shop quà và quản trị.</p>
      <a class="btn alt" href="random-quay/index.php">Vào Random Quay</a>
    </article>
  </main>
</body>

</html>