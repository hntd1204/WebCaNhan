<?php
require_once 'functions.php'; // Đã bao gồm db.php
$current_dir = getCurrentPath();
$error_msg = handleActions($conn); // Truyền biến kết nối $conn

// Thông báo
$success_msg = "";
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'created':
            $success_msg = "Tạo folder thành công! 📁";
            break;
        case 'uploaded':
            $success_msg = "Đã tải ảnh lên! 🌸";
            break;
        case 'deleted':
            $success_msg = "Đã xóa! 🗑️";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gallery (SQL Ver)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php if ($success_msg): ?>
    <div class="alert alert-success alert-float"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <div class="container py-4">
        <div class="text-center mb-4">
            <h1 class="fw-bold" style="color: #ffb7b2;">Gallery Manager 🌸</h1>
        </div>

        <div class="glass-panel p-3 mb-4">
            <div class="row g-2">
                <div class="col-md-6">
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="folder_name" class="form-control rounded-pill"
                            placeholder="Tên folder mới..." required>
                        <button name="create_folder" class="btn btn-custom rounded-pill text-nowrap">Tạo Folder</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        <input type="file" name="file_upload[]" class="form-control rounded-pill" multiple required>
                        <button class="btn btn-primary rounded-pill text-nowrap"
                            style="background:#a2d2ff; border:none;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <nav class="mb-4">
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm">
                <a href="index.php" class="text-decoration-none text-secondary"><i class="fa-solid fa-house"></i>
                    Home</a>
                <?php
                $parts = array_filter(explode('/', str_replace(ROOT_FOLDER, '', $current_dir)));
                $temp_path = ROOT_FOLDER;
                foreach ($parts as $part) {
                    $temp_path .= $part . '/';
                    echo " <span class='text-muted'>/</span> <a href='?dir=" . urlencode($temp_path) . "' class='fw-bold' style='color: #ffb7b2;'>$part</a>";
                }
                ?>
            </div>
        </nav>

        <div class="row g-3">
            <?php
            // 1. HIỂN THỊ FOLDER (Từ ổ cứng)
            $subFolders = getSubFolders($current_dir);
            foreach ($subFolders as $folder) {
                $full_path = $current_dir . $folder;
                $link_folder = '?dir=' . urlencode($full_path . '/');
                echo "
                <div class='col-6 col-md-3'>
                    <div class='item-container folder-box position-relative'>
                        <a href='$link_folder' class='text-decoration-none text-dark d-block'>
                            <div style='font-size: 2.5rem; color: #ffdac1;'><i class='fa-solid fa-folder'></i></div>
                            <div class='fw-bold mt-2 text-truncate small'>$folder</div>
                        </a>
                        <form method='POST' onsubmit=\"return confirm('Xóa folder này?');\">
                            <input type='hidden' name='delete_path' value='$full_path'>
                            <button type='submit' name='delete_item' class='btn-delete-absolute'><i class='fa-solid fa-trash'></i></button>
                        </form>
                    </div>
                </div>";
            }

            // 2. HIỂN THỊ ẢNH (Từ Database SQL)
            $files = getFilesFromDB($conn, $current_dir);
            if ($files->num_rows > 0) {
                while ($row = $files->fetch_assoc()) {
                    $file_path = $row['file_path'];
                    $file_name = $row['name'];

                    // Xử lý URL ảnh để hiển thị đúng
                    $img_parts = explode('/', $file_path);
                    $img_encoded = array_map('rawurlencode', $img_parts);
                    $img_url = implode('/', $img_encoded);

                    echo "
                    <div class='col-6 col-md-3'>
                        <div class='item-container img-box position-relative'>
                            <img src='$img_url' loading='lazy'>
                            <div class='p-2 text-center small text-muted text-truncate'>$file_name</div>
                            <form method='POST' onsubmit=\"return confirm('Xóa ảnh này?');\">
                                <input type='hidden' name='delete_path' value='$file_path'>
                                <button type='submit' name='delete_item' class='btn-delete-absolute'><i class='fa-solid fa-trash'></i></button>
                            </form>
                        </div>
                    </div>";
                }
            } else if (empty($subFolders)) {
                echo "<div class='col-12 text-center text-muted py-5'>Chưa có ảnh nào... 🌱</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Tự động ẩn thông báo sau 3s
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(el => el.remove());
    }, 3000);
    </script>
</body>

</html>