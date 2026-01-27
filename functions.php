<?php
// --- CẤU HÌNH HỆ THỐNG ---
define('ROOT_FOLDER', 'uploads/');

// Tự động tạo thư mục gốc nếu chưa có
if (!is_dir(ROOT_FOLDER)) {
    mkdir(ROOT_FOLDER, 0777, true);
}

// --- CÁC HÀM XỬ LÝ ---

// 1. Hàm lấy đường dẫn hiện tại (Bảo mật)
function getCurrentPath()
{
    $dir = isset($_GET['dir']) ? $_GET['dir'] : ROOT_FOLDER;

    // Đảm bảo đường dẫn luôn kết thúc bằng dấu /
    if (substr($dir, -1) !== '/') $dir .= '/';

    // Chặn người dùng truy cập ra khỏi thư mục uploads (../)
    if (strpos($dir, ROOT_FOLDER) !== 0 || strpos($dir, '..') !== false) {
        return ROOT_FOLDER;
    }
    return $dir;
}

// 2. Hàm xóa đệ quy (Xóa folder chứa ảnh bên trong)
function deleteRecursive($path)
{
    if (is_dir($path)) {
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                deleteRecursive($path . DIRECTORY_SEPARATOR . $item);
            }
        }
        return rmdir($path);
    } elseif (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

// 3. Hàm xử lý chính (Tạo, Up, Xóa)
function handleActions()
{
    $current_dir = getCurrentPath();

    // A. XỬ LÝ TẠO THƯ MỤC
    if (isset($_POST['create_folder'])) {
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folder_name']);
        if ($name) {
            $new_path = $current_dir . $name . '/';
            if (!is_dir($new_path)) {
                if (mkdir($new_path, 0777, true)) {
                    header("Location: index.php?dir=" . $current_dir . "&msg=created");
                    exit;
                } else {
                    return "Lỗi: Server không cho phép tạo thư mục (Cần set quyền 777).";
                }
            } else {
                return "Tên thư mục này đã tồn tại!";
            }
        }
    }

    // B. XỬ LÝ UPLOAD ẢNH
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file = $_FILES['file_upload'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        if (in_array($ext, $allowed)) {
            // Tạo tên file ngẫu nhiên để tránh trùng
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file['name']);
            $dest = $current_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                header("Location: index.php?dir=" . $current_dir . "&msg=uploaded");
                exit;
            } else {
                return "Lỗi: Không thể lưu file (Kiểm tra dung lượng hoặc quyền ghi).";
            }
        } else {
            return "Chỉ hỗ trợ file ảnh (JPG, PNG, WEBP...)";
        }
    }

    // C. XỬ LÝ XÓA
    if (isset($_POST['delete_item'])) {
        $path_to_delete = $_POST['delete_path'];

        // Kiểm tra an toàn: Chỉ xóa nếu đường dẫn nằm trong uploads/ và không phải là thư mục gốc
        if (strpos($path_to_delete, ROOT_FOLDER) === 0 && rtrim($path_to_delete, '/') !== rtrim(ROOT_FOLDER, '/')) {
            deleteRecursive($path_to_delete);
            header("Location: index.php?dir=" . $current_dir . "&msg=deleted");
            exit;
        } else {
            return "Cảnh báo: Hành động xóa không hợp lệ!";
        }
    }

    return ""; // Không có lỗi
}

// 4. Lấy danh sách file/folder
function getFiles($dir)
{
    if (!is_dir($dir)) return [];
    return array_diff(scandir($dir), ['.', '..']);
}