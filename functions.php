<?php
require_once 'db.php'; // Đã có session_start

// Sử dụng đường dẫn tuyệt đối để tránh lỗi khi include ở các thư mục con
define('ROOT_PATH', __DIR__ . '/uploads/');
define('ROOT_URL', 'uploads/'); // Đường dẫn tương đối cho trình duyệt

if (!is_dir(ROOT_PATH)) {
    mkdir(ROOT_PATH, 0755, true);
}

function getCurrentPath()
{
    $dir = isset($_GET['dir']) ? urldecode($_GET['dir']) : ROOT_URL;

    // Bảo mật: Chặn path traversal (../)
    $real_root = realpath(ROOT_PATH);
    $check_path = realpath(__DIR__ . '/' . $dir);

    if ($check_path === false || strpos($check_path, $real_root) !== 0) {
        return ROOT_URL;
    }

    // Đảm bảo luôn có dấu / ở cuối
    return rtrim($dir, '/') . '/';
}

function deleteFolderRecursive($path, $conn)
{
    // Chuyển đường dẫn tương đối (URL) sang đường dẫn vật lý
    $physical_path = __DIR__ . '/' . $path;

    // 1. Xóa SQL
    $sql_path = $conn->real_escape_string($path);
    $conn->query("DELETE FROM gallery WHERE file_path LIKE '$sql_path%'");

    // 2. Xóa vật lý
    if (is_dir($physical_path)) {
        $items = scandir($physical_path);
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                // Đệ quy với đường dẫn tương đối
                deleteFolderRecursive($path . $item . (is_dir($physical_path . '/' . $item) ? '/' : ''), $conn);
            }
        }
        return rmdir($physical_path);
    } elseif (file_exists($physical_path)) {
        return unlink($physical_path);
    }
    return false;
}

function handleActions($conn)
{
    $current_dir = getCurrentPath();
    $physical_dir = __DIR__ . '/' . $current_dir;

    // A. TẠO THƯ MỤC
    if (isset($_POST['create_folder'])) {
        $raw_name = trim($_POST['folder_name']);
        $folder_name = preg_replace('/[^A-Za-z0-9_\-\p{L}\s]/u', '', $raw_name); // Chỉ cho phép ký tự an toàn

        if (!empty($folder_name)) {
            $new_path = $physical_dir . $folder_name;
            if (!is_dir($new_path)) {
                if (mkdir($new_path, 0755, true)) {
                    header("Location: index.php?dir=" . urlencode($current_dir . $folder_name . '/') . "&msg=created");
                    exit;
                }
            }
        }
    }

    // B. UPLOAD ẢNH (Drag & Drop hỗ trợ)
    if (isset($_FILES['file_upload'])) {
        $count = count($_FILES['file_upload']['name']);
        $success_count = 0;

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['file_upload']['error'][$i] == 0) {
                $raw_name = $_FILES['file_upload']['name'][$i];
                $tmp_name = $_FILES['file_upload']['tmp_name'][$i];
                $ext = strtolower(pathinfo($raw_name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

                if (in_array($ext, $allowed)) {
                    // Tên file an toàn hơn
                    $clean_name = pathinfo($raw_name, PATHINFO_FILENAME);
                    $clean_name = preg_replace('/[^A-Za-z0-9_\-\p{L}]/u', '', $clean_name);
                    $filename = $clean_name . '_' . time() . '_' . $i . '.' . $ext;

                    $dest_rel = $current_dir . $filename; // Đường dẫn lưu DB
                    $dest_abs = $physical_dir . $filename; // Đường dẫn lưu File

                    if (move_uploaded_file($tmp_name, $dest_abs)) {
                        chmod($dest_abs, 0644);

                        $stmt = $conn->prepare("INSERT INTO gallery (name, file_path, folder_path) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $raw_name, $dest_rel, $current_dir);
                        $stmt->execute();
                        $stmt->close();
                        $success_count++;
                    }
                }
            }
        }
        if ($success_count > 0) {
            header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=uploaded");
            exit;
        }
    }

    // C. XÓA ITEM
    if (isset($_POST['delete_item'])) {
        $path_to_delete = $_POST['delete_path'];
        deleteFolderRecursive($path_to_delete, $conn);
        header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=deleted");
        exit;
    }
}

function getSubFolders($rel_dir)
{
    $abs_dir = __DIR__ . '/' . $rel_dir;
    if (!is_dir($abs_dir)) return [];
    $items = scandir($abs_dir);
    $folders = [];
    foreach ($items as $item) {
        if ($item != '.' && $item != '..' && is_dir($abs_dir . $item)) {
            $folders[] = $item;
        }
    }
    return $folders;
}

function getFilesFromDB($conn, $current_dir, $search_query = '')
{
    if ($search_query) {
        // Chế độ tìm kiếm (toàn bộ thư viện)
        $term = "%$search_query%";
        $stmt = $conn->prepare("SELECT * FROM gallery WHERE name LIKE ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $term);
    } else {
        // Chế độ duyệt thư mục
        $stmt = $conn->prepare("SELECT * FROM gallery WHERE folder_path = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $current_dir);
    }
    $stmt->execute();
    return $stmt->get_result();
}