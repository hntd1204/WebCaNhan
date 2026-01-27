<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = $_POST['target_dir'];
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        header("Location: index.php?dir=" . $target_dir);
    } else {
        echo "Có lỗi xảy ra khi upload.";
    }
}