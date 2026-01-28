<?php
require_once 'db.php';

define('ROOT_FOLDER', 'uploads/');

if (!is_dir(ROOT_FOLDER)) {
    mkdir(ROOT_FOLDER, 0755, true);
}

function getCurrentPath()
{
    $dir = isset($_GET['dir']) ? urldecode($_GET['dir']) : ROOT_FOLDER;
    if (substr($dir, -1) !== '/') $dir .= '/';
    if (strpos($dir, ROOT_FOLDER) !== 0 || strpos($dir, '..') !== false) {
        return ROOT_FOLDER;
    }
    return $dir;
}

// Hàm xóa folder và dữ liệu trong SQL
function deleteFolderRecursive($path, $conn)
{
    // 1. Xóa dữ liệu trong SQL trước (những file nằm trong folder này)
    // Lưu ý: Thêm dấu % để xóa tất cả file con trong sub-folder
    $sql_path = $conn->real_escape_string($path);
    $conn->query("DELETE FROM gallery WHERE file_path LIKE '$sql_path%'");

    // 2. Xóa file vật lý
    if (is_dir($path)) {
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                deleteFolderRecursive($path . DIRECTORY_SEPARATOR . $item, $conn);
            }
        }
        return rmdir($path);
    } elseif (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

function handleActions($conn)
{
    $current_dir = getCurrentPath();

    // A. TẠO THƯ MỤC
    if (isset($_POST['create_folder'])) {
        $raw_name = trim($_POST['folder_name']);
        $folder_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '', $raw_name);

        if (!empty($folder_name)) {
            $new_path = $current_dir . $folder_name . '/';
            if (!is_dir($new_path)) {
                if (mkdir($new_path, 0755, true)) {
                    header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=created");
                    exit;
                }
            }
        }
    }

    // B. UPLOAD ẢNH (CÓ SQL)
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
                    $clean_name = pathinfo($raw_name, PATHINFO_FILENAME);
                    // Tạo tên file unique
                    $filename = $clean_name . '_' . time() . '_' . $i . '.' . $ext;
                    $dest = $current_dir . $filename;

                    if (move_uploaded_file($tmp_name, $dest)) {
                        chmod($dest, 0644); // Cấp quyền đọc file

                        // INSERT VÀO DATABASE
                        $stmt = $conn->prepare("INSERT INTO gallery (name, file_path, folder_path) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $raw_name, $dest, $current_dir);
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

        // Kiểm tra bảo mật đường dẫn
        if (strpos($path_to_delete, ROOT_FOLDER) === 0 && rtrim($path_to_delete, '/') !== rtrim(ROOT_FOLDER, '/')) {

            if (is_dir($path_to_delete)) {
                // Nếu là folder: Gọi hàm xóa đệ quy (xóa cả SQL lẫn file)
                deleteFolderRecursive($path_to_delete, $conn);
            } else {
                // Nếu là file: Xóa trong SQL trước, rồi xóa file
                $stmt = $conn->prepare("DELETE FROM gallery WHERE file_path = ?");
                $stmt->bind_param("s", $path_to_delete);
                $stmt->execute();
                $stmt->close();

                if (file_exists($path_to_delete)) {
                    unlink($path_to_delete);
                }
            }

            header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=deleted");
            exit;
        }
    }
    return "";
}

// Hàm lấy danh sách Folder (vẫn quét từ ổ cứng cho chính xác)
function getSubFolders($dir)
{
    if (!is_dir($dir)) return [];
    $items = scandir($dir);
    $folders = [];
    foreach ($items as $item) {
        if ($item != '.' && $item != '..' && is_dir($dir . $item)) {
            $folders[] = $item;
        }
    }
    return $folders;
}

// Hàm lấy danh sách File (Lấy từ SQL)
function getFilesFromDB($conn, $current_dir)
{
    $stmt = $conn->prepare("SELECT * FROM gallery WHERE folder_path = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $current_dir);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}