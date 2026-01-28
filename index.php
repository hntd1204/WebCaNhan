<?php
require_once 'db.php';
require_once 'check_login.php'; // BẢO VỆ TRANG
require_once 'functions.php';

$current_dir = getCurrentPath();
handleActions($conn);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$success_msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Map message code
$msgs = [
    'created' => 'Tạo folder thành công! 📁',
    'uploaded' => 'Đã tải ảnh lên! 🌸',
    'deleted' => 'Đã xóa dữ liệu! 🗑️'
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gallery Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php if (isset($msgs[$success_msg])): ?>
    <div class="alert alert-success alert-float"><?= $msgs[$success_msg]; ?></div>
    <?php endif; ?>

    <nav class="navbar navbar-expand-lg glass-header sticky-top mb-4 px-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" style="color: #ff8fa3;" href="index.php">
                <i class="fa-solid fa-photo-film"></i> Gallery Pro
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-md-block">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill"><i
                        class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">

        <div class="glass-panel p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <form method="GET" class="position-relative">
                        <input type="text" name="search" class="form-control rounded-pill ps-4"
                            value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm file...">
                        <button type="submit" class="btn position-absolute top-0 end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="folder_name" class="form-control rounded-pill"
                            placeholder="Tên folder..." required>
                        <button name="create_folder" class="btn btn-custom rounded-pill"><i
                                class="fa-solid fa-folder-plus"></i></button>
                    </form>
                </div>
                <div class="col-md-4">
                    <label for="file_upload" class="upload-zone d-block text-center rounded-pill cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Chọn hoặc thả ảnh vào đây
                    </label>
                    <form method="POST" enctype="multipart/form-data" id="uploadForm" class="d-none">
                        <input type="file" name="file_upload[]" id="file_upload" multiple
                            onchange="document.getElementById('uploadForm').submit()">
                    </form>
                </div>
            </div>
        </div>

        <?php if (!$search): ?>
        <nav class="mb-4 breadcrumb-scroll">
            <div class="bg-white px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center">
                <a href="index.php" class="text-secondary text-decoration-none"><i class="fa-solid fa-house"></i></a>
                <?php
                    $parts = array_filter(explode('/', trim(str_replace('uploads/', '', $current_dir), '/')));
                    $temp_path = 'uploads/';
                    foreach ($parts as $part) {
                        if (empty($part)) continue;
                        $temp_path .= $part . '/';
                        echo " <span class='mx-2 text-muted'>/</span> ";
                        echo "<a href='?dir=" . urlencode($temp_path) . "' class='fw-bold text-decoration-none' style='color: #ffb7b2;'>$part</a>";
                    }
                    ?>
            </div>
        </nav>
        <?php else: ?>
        <h5 class="mb-3 text-muted">Kết quả tìm kiếm cho: "<?= htmlspecialchars($search) ?>" <a href="index.php"
                class="small ms-2">Xóa lọc</a></h5>
        <?php endif; ?>

        <div class="row g-3">
            <?php
            // 1. FOLDERS (Ẩn khi đang search)
            if (!$search) {
                $subFolders = getSubFolders($current_dir);
                foreach ($subFolders as $folder) {
                    $full_path = $current_dir . $folder . '/';
                    echo "
                    <div class='col-6 col-sm-4 col-md-3 col-lg-2'>
                        <div class='item-container folder-box position-relative h-100'>
                            <a href='?dir=" . urlencode($full_path) . "' class='text-decoration-none text-dark d-flex flex-column align-items-center justify-content-center h-100 p-3'>
                                <i class='fa-solid fa-folder' style='font-size: 3rem; color: #ffdac1;'></i>
                                <div class='fw-bold mt-2 text-truncate w-100 text-center small'>$folder</div>
                            </a>
                            <form method='POST' onsubmit=\"return confirm('Xóa folder này và toàn bộ ảnh bên trong?');\">
                                <input type='hidden' name='delete_path' value='$full_path'>
                                <button type='submit' name='delete_item' class='btn-action btn-delete'><i class='fa-solid fa-trash'></i></button>
                            </form>
                        </div>
                    </div>";
                }
            }

            // 2. FILES
            $files = getFilesFromDB($conn, $current_dir, $search);
            if ($files->num_rows > 0) {
                while ($row = $files->fetch_assoc()) {
                    $file_path = $row['file_path']; // ex: uploads/folder/anh.jpg
                    $file_name = $row['name'];
                    // Encode URL để tránh lỗi dấu cách/tiếng Việt
                    $url_parts = explode('/', $file_path);
                    $url_parts = array_map('rawurlencode', $url_parts);
                    $img_url = implode('/', $url_parts);

                    echo "
                    <div class='col-6 col-sm-4 col-md-3 col-lg-2'>
                        <div class='item-container img-box position-relative h-100'>
                            <a href='$img_url' data-fancybox='gallery' data-caption='$file_name'>
                                <img src='$img_url' loading='lazy' alt='$file_name'>
                            </a>
                            <div class='p-2 text-center'>
                                <div class='small text-muted text-truncate'>$file_name</div>
                            </div>
                            
                            <div class='action-group'>
                                <button class='btn-action btn-copy' onclick=\"copyToClipboard('$img_url')\" title='Copy Link'>
                                    <i class='fa-regular fa-copy'></i>
                                </button>
                                <form method='POST' onsubmit=\"return confirm('Xóa ảnh này?');\" class='d-inline'>
                                    <input type='hidden' name='delete_path' value='$file_path'>
                                    <button type='submit' name='delete_item' class='btn-action btn-delete' title='Xóa'>
                                        <i class='fa-solid fa-trash'></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>";
                }
            } elseif (empty($subFolders) && !$search) {
                echo "<div class='col-12 text-center text-muted py-5'><i class='fa-regular fa-folder-open display-4 opacity-50'></i><br>Chưa có dữ liệu</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
    // Kích hoạt Fancybox
    Fancybox.bind("[data-fancybox]", {});

    // Auto hide alert
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(el => el.remove());
    }, 3000);

    // Copy Link
    function copyToClipboard(text) {
        const fullLink = window.location.origin + window.location.pathname.replace('index.php', '') + text;
        navigator.clipboard.writeText(fullLink).then(() => {
            alert('Đã copy link ảnh: ' + fullLink);
        });
    }

    // Hiệu ứng Drag & Drop
    const dropZone = document.querySelector('.glass-panel');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    </script>
</body>

</html>