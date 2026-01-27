<?php
require_once 'functions.php';
$current_dir = getCurrentPath();
$error_msg = handleActions();

// Lấy thông báo thành công từ URL
$success_msg = "";
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'created':
            $success_msg = "Đã tạo thư mục mới! 📁";
            break;
        case 'uploaded':
            $success_msg = "Đã tải ảnh lên thành công! 🌸";
            break;
        case 'deleted':
            $success_msg = "Đã xóa thành công! 🗑️";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>My Sweet Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container py-5">

        <div class="text-center mb-5">
            <h1 class="fw-bold" style="color: #ffb7b2;">🌸 MY SWEET DRIVE</h1>

            <?php if ($error_msg): ?>
            <div class="alert alert-danger mt-3"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if ($success_msg): ?>
            <div class="alert alert-success mt-3" style="background:#e2f0cb; border:none; color:#5c7c59;">
                <?php echo $success_msg; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="glass-panel p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6 border-end">
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="folder_name" class="form-control rounded-pill"
                            placeholder="Tên thư mục mới..." required>
                        <button name="create_folder" class="btn btn-custom rounded-pill text-nowrap">
                            <i class="fa-solid fa-plus"></i> Tạo Folder
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        <input type="file" name="file_upload" class="form-control rounded-pill" required>
                        <button class="btn btn-primary rounded-pill text-nowrap"
                            style="background-color: #a2d2ff; border:none;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Tải Lên
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <nav class="mb-4">
            <div class="bg-white px-4 py-2 rounded-pill d-inline-block shadow-sm">
                <a href="index.php" class="text-decoration-none text-secondary"><i class="fa-solid fa-house"></i>
                    Home</a>
                <?php
                // Xử lý hiển thị đường dẫn
                $parts = array_filter(explode('/', str_replace(ROOT_FOLDER, '', $current_dir)));
                $temp_path = ROOT_FOLDER;
                foreach ($parts as $part) {
                    $temp_path .= $part . '/';
                    echo " <span class='text-muted mx-1'>/</span> <a href='?dir=$temp_path' class='text-decoration-none fw-bold' style='color: #ffb7b2;'>$part</a>";
                }
                ?>
            </div>
        </nav>

        <div class="row g-4">
            <?php
            $items = getFiles($current_dir);

            if (empty($items)) {
                echo "<div class='text-center py-5 text-muted'>Thư mục này đang trống... 🌱</div>";
            }

            foreach ($items as $item) {
                $full_path = $current_dir . $item;

                // --- NẾU LÀ THƯ MỤC ---
                if (is_dir($full_path)) {
                    echo "
                <div class='col-6 col-md-3'>
                    <div class='item-container folder-box position-relative'>
                        <a href='?dir=$full_path/' class='text-decoration-none text-dark d-block'>
                            <div style='font-size: 3rem; color: #ffdac1;'><i class='fa-solid fa-folder'></i></div>
                            <div class='fw-bold mt-2 text-truncate'>$item</div>
                        </a>
                        
                        <form method='POST' onsubmit=\"return confirm('CẢNH BÁO: Bạn có chắc muốn xóa thư mục này và TOÀN BỘ ảnh bên trong?');\">
                            <input type='hidden' name='delete_path' value='$full_path'>
                            <button type='submit' name='delete_item' class='btn-delete-absolute' title='Xóa thư mục'>
                                <i class='fa-solid fa-trash'></i>
                            </button>
                        </form>
                    </div>
                </div>";
                }
                // --- NẾU LÀ ẢNH ---
                else {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        echo "
                    <div class='col-6 col-md-4 col-lg-3'>
                        <div class='item-container img-box position-relative'>
                            <img src='$full_path' alt='$item' loading='lazy'>
                            <div class='p-2 text-center small text-muted text-truncate bg-white'>$item</div>
                            
                            <form method='POST' onsubmit=\"return confirm('Bạn muốn xóa ảnh này?');\">
                                <input type='hidden' name='delete_path' value='$full_path'>
                                <button type='submit' name='delete_item' class='btn-delete-absolute' title='Xóa ảnh'>
                                    <i class='fa-solid fa-trash'></i>
                                </button>
                            </form>
                        </div>
                    </div>";
                    }
                }
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>