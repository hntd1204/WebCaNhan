<?php
define('ROOT_FOLDER', 'uploads/');

if (!is_dir(ROOT_FOLDER)) {
    mkdir(ROOT_FOLDER, 0777, true);
}

// 1. Hàm lấy đường dẫn (Cần giải mã URL vì trên link nó bị mã hóa)
function getCurrentPath()
{
    $dir = isset($_GET['dir']) ? urldecode($_GET['dir']) : ROOT_FOLDER;

    if (substr($dir, -1) !== '/') $dir .= '/';

    // Bảo mật
    if (strpos($dir, ROOT_FOLDER) !== 0 || strpos($dir, '..') !== false) {
        return ROOT_FOLDER;
    }
    return $dir;
}

// 2. Hàm xóa đệ quy
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

// 3. Hàm xử lý chính
function handleActions()
{
    $current_dir = getCurrentPath();

    // A. TẠO THƯ MỤC (GIỮ NGUYÊN TÊN, CHỈ LỌC KÝ TỰ CẤM CỦA WINDOWS)
    if (isset($_POST['create_folder'])) {
        $raw_name = trim($_POST['folder_name']);

        // Loại bỏ các ký tự đặc biệt khiến hệ điều hành lỗi: \ / : * ? " < > |
        $folder_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '', $raw_name);

        if (!empty($folder_name)) {
            $new_path = $current_dir . $folder_name . '/';

            if (!is_dir($new_path)) {
                // Trên Windows XAMPP đôi khi cần iconv để xử lý tiếng Việt, nhưng thử mkdir chuẩn trước
                if (mkdir($new_path, 0777, true)) {
                    // Redirect cần urlencode đường dẫn để không lỗi khoảng trắng
                    header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=created");
                    exit;
                } else {
                    return "Lỗi: Không thể tạo thư mục (Có thể do tên chứa ký tự lạ hoặc lỗi quyền).";
                }
            } else {
                return "Thư mục này đã tồn tại!";
            }
        }
    }

    // B. UPLOAD ẢNH (GIỮ NGUYÊN TÊN FILE GỐC)
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file = $_FILES['file_upload'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        if (in_array($ext, $allowed)) {
            // Lấy tên gốc, chỉ bỏ ký tự cấm
            $name_only = pathinfo($file['name'], PATHINFO_FILENAME);
            $clean_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '', $name_only);

            // Thêm số ngẫu nhiên để tránh trùng nhưng vẫn giữ tên đẹp
            $filename = $clean_name . '_' . time() . '.' . $ext;
            $dest = $current_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=uploaded");
                exit;
            }
        } else {
            return "Chỉ hỗ trợ file ảnh.";
        }
    }

    // C. XÓA
    if (isset($_POST['delete_item'])) {
        $path_to_delete = $_POST['delete_path'];
        if (strpos($path_to_delete, ROOT_FOLDER) === 0 && rtrim($path_to_delete, '/') !== rtrim(ROOT_FOLDER, '/')) {
            deleteRecursive($path_to_delete);
            header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=deleted");
            exit;
        }
    }

    return "";
}

function getFiles($dir)
{
    if (!is_dir($dir)) return [];
    return array_diff(scandir($dir), ['.', '..']);
}