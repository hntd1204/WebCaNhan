<?php
define('ROOT_FOLDER', 'uploads/');

if (!is_dir(ROOT_FOLDER)) {
    mkdir(ROOT_FOLDER, 0777, true);
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

function handleActions()
{
    $current_dir = getCurrentPath();

    // A. TẠO THƯ MỤC
    if (isset($_POST['create_folder'])) {
        $raw_name = trim($_POST['folder_name']);
        $folder_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '', $raw_name);

        if (!empty($folder_name)) {
            $new_path = $current_dir . $folder_name . '/';
            if (!is_dir($new_path)) {
                if (mkdir($new_path, 0777, true)) {
                    header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=created");
                    exit;
                }
            }
        }
    }

    // B. UPLOAD NHIỀU ẢNH (LOGIC MỚI)
    if (isset($_FILES['file_upload'])) {
        $count = count($_FILES['file_upload']['name']); // Đếm số file được chọn
        $success_count = 0;

        // Chạy vòng lặp xử lý từng file
        for ($i = 0; $i < $count; $i++) {
            // Kiểm tra lỗi của từng file
            if ($_FILES['file_upload']['error'][$i] == 0) {
                $raw_name = $_FILES['file_upload']['name'][$i];
                $tmp_name = $_FILES['file_upload']['tmp_name'][$i];

                $ext = strtolower(pathinfo($raw_name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

                if (in_array($ext, $allowed)) {
                    // Làm sạch tên file
                    $name_only = pathinfo($raw_name, PATHINFO_FILENAME);
                    $clean_name = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '', $name_only);

                    // Tạo tên file mới
                    $filename = $clean_name . '_' . time() . '_' . $i . '.' . $ext; // Thêm $i để tránh trùng nếu up nhiều ảnh cùng tên
                    $dest = $current_dir . $filename;

                    if (move_uploaded_file($tmp_name, $dest)) {
                        $success_count++;
                    }
                }
            }
        }

        // Nếu có ít nhất 1 file up thành công
        if ($success_count > 0) {
            header("Location: index.php?dir=" . urlencode($current_dir) . "&msg=uploaded");
            exit;
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