<?php
session_start();
require 'db.php';

// --- KIỂM TRA QUYỀN ---
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);

// --- XỬ LÝ LOGOUT ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- CẤU TRÚC DỮ LIỆU: LẤY TỪ DATABASE ---
$locations = [];

// 1. Lấy tất cả thành phố
$stmtCities = $pdo->query("SELECT * FROM cities ORDER BY name ASC");
$dbCities = $stmtCities->fetchAll(PDO::FETCH_ASSOC);

foreach ($dbCities as $city) {
    $locations[$city['name']] = [];
    // 2. Lấy quận/huyện
    $stmtDistricts = $pdo->prepare("SELECT name FROM districts WHERE city_id = ? ORDER BY name ASC");
    $stmtDistricts->execute([$city['id']]);
    $dbDistricts = $stmtDistricts->fetchAll(PDO::FETCH_COLUMN);

    if ($dbDistricts) {
        $locations[$city['name']] = $dbDistricts;
    }
}

// --- CÁC HÀM HỖ TRỢ ---
function getCoordinatesFromUrl($url)
{
    preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches);
    if (isset($matches[1]) && isset($matches[2])) {
        return ['lat' => $matches[1], 'lng' => $matches[2]];
    }
    return null;
}

// Hàm xử lý upload nhiều ảnh
// Hàm xử lý thu nhỏ và chuyển đổi ảnh sang WebP
function compressAndConvertToWebP($sourcePath, $destinationPath, $ext, $maxWidth = 800)
{
    // 1. Đọc ảnh vào bộ nhớ tùy theo định dạng
    switch ($ext) {
        case 'jpeg':
        case 'jpg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        case 'webp':
            $image = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$image) return false;

    // 2. Lấy kích thước gốc
    $origWidth = imagesx($image);
    $origHeight = imagesy($image);

    // 3. Nếu ảnh to hơn maxWidth (800px), tiến hành thu nhỏ (Resize)
    if ($origWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = intval($origHeight * ($maxWidth / $origWidth));
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Xử lý giữ nền trong suốt nếu ảnh gốc là PNG/GIF
        if ($ext == 'png' || $ext == 'gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($image);
        $image = $newImage; // Gán lại ảnh đã resize
    } else {
        // Vẫn xử lý alpha cho ảnh nhỏ
        if ($ext == 'png' || $ext == 'gif') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
    }

    // 4. Lưu ảnh mới ra file với định dạng WebP, chất lượng 80%
    $result = imagewebp($image, $destinationPath, 80);
    imagedestroy($image);

    return $result;
}

// Hàm upload nhiều ảnh (Đã được nâng cấp)
function handleMultipleUploads($fileArray)
{
    $uploadedPaths = [];
    $uploadDir = 'uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (isset($fileArray['name']) && is_array($fileArray['name'])) {
        $count = count($fileArray['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($fileArray['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $fileArray['tmp_name'][$i];
                $ext = strtolower(pathinfo($fileArray['name'][$i], PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    // CỐ ĐỊNH LƯU THÀNH ĐUÔI .webp
                    $fileName = uniqid() . '_' . time() . '.webp';
                    $targetFilePath = $uploadDir . $fileName;

                    // Chuyển việc lưu file qua hàm xử lý bên trên
                    if (compressAndConvertToWebP($tmpName, $targetFilePath, $ext)) {
                        $uploadedPaths[] = $targetFilePath;
                    }
                }
            }
        }
    }
    return $uploadedPaths;
}

// --- XỬ LÝ POST (QUAN TRỌNG: CHẶN NẾU KHÔNG PHẢI ADMIN) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        die("Hành động bị từ chối. Bạn không có quyền Admin.");
    }

    if (isset($_POST['action']) && $_POST['action'] == 'add_category') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([trim($_POST['category_name'])]);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] == 'update_category') {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([trim($_POST['cat_name']), $_POST['cat_id']]);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] == 'delete_category') {
        $catId = $_POST['cat_id'];
        $pdo->prepare("UPDATE places SET category_id = NULL WHERE category_id = ?")->execute([$catId]);
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$catId]);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] == 'add_city') {
        $stmt = $pdo->prepare("INSERT INTO cities (name) VALUES (?)");
        $stmt->execute([trim($_POST['city_name'])]);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] == 'add_district') {
        $stmt = $pdo->prepare("INSERT INTO districts (city_id, name) VALUES (?, ?)");
        $stmt->execute([$_POST['city_id'], trim($_POST['district_name'])]);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'add_place') {
        $lat = null;
        $lng = null;
        $originalLink = $_POST['map_url'] ?? '';

        $uploadedImages = handleMultipleUploads($_FILES['upload_images'] ?? []);
        $imagesStr = implode("\n", $uploadedImages);

        if (!empty($originalLink)) {
            $coords = getCoordinatesFromUrl($originalLink);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        $catId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        $sql = "INSERT INTO places (name, category_id, city, district, address, description, latitude, longitude, rating, original_link, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$_POST['name'], $catId, $_POST['city'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating'], $originalLink, $imagesStr]);
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'edit_place') {
        $lat = !empty($_POST['current_lat']) ? $_POST['current_lat'] : null;
        $lng = !empty($_POST['current_lng']) ? $_POST['current_lng'] : null;
        $originalLink = $_POST['map_url'] ?? '';

        $keepImages = $_POST['keep_images'] ?? [];
        $newUploadedImages = handleMultipleUploads($_FILES['upload_images'] ?? []);
        $allImagesArr = array_merge($keepImages, $newUploadedImages);
        $finalImagesStr = implode("\n", $allImagesArr);

        if (!empty($originalLink)) {
            $coords = getCoordinatesFromUrl($originalLink);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        $catId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        $sql = "UPDATE places SET name=?, category_id=?, city=?, district=?, address=?, description=?, latitude=?, longitude=?, rating=?, original_link=?, images=? WHERE id=?";
        $pdo->prepare($sql)->execute([$_POST['name'], $catId, $_POST['city'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating'], $originalLink, $finalImagesStr, $_POST['id']]);
        header("Location: index.php");
        exit;
    }
}

// --- XỬ LÝ GET XÓA ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if (!$isAdmin) die("Bạn không có quyền xóa.");
    $pdo->prepare("DELETE FROM places WHERE id = ?")->execute([$_GET['id']]);
    header("Location: index.php");
    exit;
}

// --- LẤY DỮ LIỆU & XỬ LÝ LỌC / TÌM QUANH ĐÂY ---
$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$userLat = $_GET['user_lat'] ?? null;
$userLng = $_GET['user_lng'] ?? null;
$sortDistance = isset($_GET['sort']) && $_GET['sort'] === 'distance';

$params = [];
$filterCity = $_GET['filter_city'] ?? '';
$filterDistrict = $_GET['filter_district'] ?? '';
$filterCategory = $_GET['filter_category'] ?? '';
$search = $_GET['search'] ?? '';

// Nếu có tọa độ thì thêm công thức tính khoảng cách
if ($userLat && $userLng && $sortDistance) {
    $sqlPlace = "SELECT places.*, categories.name as category_name, 
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance 
        FROM places 
        LEFT JOIN categories ON places.category_id = categories.id 
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
    $params = [$userLat, $userLng, $userLat];
} else {
    $sqlPlace = "SELECT places.*, categories.name as category_name FROM places LEFT JOIN categories ON places.category_id = categories.id WHERE 1=1";
}

if (!empty($filterCity)) {
    $sqlPlace .= " AND places.city = ?";
    $params[] = $filterCity;
}
if (!empty($filterDistrict)) {
    $sqlPlace .= " AND places.district = ?";
    $params[] = $filterDistrict;
}
if (!empty($filterCategory)) {
    $sqlPlace .= " AND places.category_id = ?";
    $params[] = $filterCategory;
}
if (!empty($search)) {
    $sqlPlace .= " AND (places.name LIKE ? OR places.address LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Sắp xếp theo khoảng cách hoặc Random
if ($userLat && $userLng && $sortDistance) {
    $sqlPlace .= " ORDER BY distance ASC";
} else {
    $sqlPlace .= " ORDER BY RAND()";
}

$stmt = $pdo->prepare($sqlPlace);
$stmt->execute($params);
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Thành Đạt</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <link rel="stylesheet" href="style.css">

    <style>
        .thumbnail-wrapper {
            cursor: pointer;
        }

        .thumbnail-wrapper .place-thumbnail {
            transition: transform 0.4s ease, opacity 0.4s ease;
        }

        .thumbnail-wrapper:hover .place-thumbnail {
            transform: scale(1.08);
            opacity: 1 !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-journal-richtext text-secondary me-2 fs-3"></i> Địa điểm của Thành Đạt
            </a>

            <div class="d-flex align-items-center gap-2">
                <?php if ($isLoggedIn): ?>
                    <span class="d-none d-sm-inline">Chào, <b><?= htmlspecialchars($_SESSION['username']) ?></b>
                        (<?= $isAdmin ? 'Admin' : 'Xem' ?>)</span>
                    <a href="index.php?action=logout" class="btn btn-sm btn-outline-danger fw-bold">Thoát</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-primary fw-bold">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <?php if ($isAdmin): ?>
                    <button class="btn btn-primary w-100 mb-3 d-lg-none btn-mobile-toggle fw-bold" type="button"
                        data-bs-toggle="collapse" data-bs-target="#formCollapse">
                        <i class="bi bi-plus-circle-fill me-2"></i> Viết Check-in Mới
                    </button>

                    <div class="collapse d-lg-block" id="formCollapse">
                        <div class="card card-form sticky-lg-top" style="top: 90px; z-index: 10;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pen text-primary me-2"></i>Check-in</h5>
                                <button type="button" class="btn-close d-lg-none" data-bs-toggle="collapse"
                                    data-bs-target="#formCollapse"></button>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="add_place">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="name" class="form-control" id="floatingName" required
                                            placeholder="Tên quán">
                                        <label for="floatingName">Tên địa điểm / Quán ăn</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <select name="category_id" class="form-select" id="floatingCat">
                                            <?php foreach ($cats as $cat): ?><option value="<?= $cat['id'] ?>">
                                                    <?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
                                        </select>
                                        <label for="floatingCat">Danh mục</label>
                                        <div class="position-absolute top-50 end-0 translate-middle-y me-2">
                                            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="modal"
                                                data-bs-target="#catModal"><i class="bi bi-gear"></i></button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating">
                                                <select name="city" class="form-select" id="add_city"
                                                    onchange="updateDistricts('add_city', 'add_district')">
                                                    <?php foreach (array_keys($locations) as $city): ?><option
                                                            value="<?= $city ?>"><?= $city ?></option><?php endforeach; ?>
                                                </select>
                                                <label>Thành phố</label>
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-2">
                                                    <button class="btn btn-sm btn-light border" type="button"
                                                        data-bs-toggle="modal" data-bs-target="#locModal"
                                                        title="Thêm khu vực"><i class="bi bi-gear"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating">
                                                <select name="district" class="form-select" id="add_district">
                                                    <option value="">-- Chọn TP trước --</option>
                                                </select>
                                                <label>Quận / Huyện</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="url" name="map_url" class="form-control" id="floatingLink" required
                                            placeholder="Link Map">
                                        <label for="floatingLink"><i class="bi bi-link-45deg text-danger me-1"></i> Dán link
                                            Google Map</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="address" class="form-control" id="floatingAddress"
                                            placeholder="Địa chỉ">
                                        <label for="floatingAddress">Địa chỉ chi tiết</label>
                                    </div>

                                    <div class="mb-3 p-2 border rounded bg-light">
                                        <label class="form-label fw-bold text-success mb-1"><i
                                                class="bi bi-upload me-1"></i> Tải ảnh lên (Chọn nhiều ảnh)</label>
                                        <input type="file" name="upload_images[]"
                                            class="form-control bg-white form-control-sm" multiple accept="image/*">
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="rating" class="form-select" id="floatingRating">
                                            <option value="5">⭐⭐⭐⭐⭐ (5 - Tuyệt vời)</option>
                                            <option value="4">⭐⭐⭐⭐ (4 - Ngon)</option>
                                            <option value="3">⭐⭐⭐ (3 - Ổn)</option>
                                            <option value="2">⭐⭐ (2 - Tệ)</option>
                                            <option value="1">⭐ (1 - Rất tệ)</option>
                                        </select>
                                        <label for="floatingRating">Đánh giá</label>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <textarea name="description" class="form-control" id="floatingDesc"
                                            style="height: 100px" placeholder="Ghi chú"></textarea>
                                        <label for="floatingDesc">Ghi chú (Món ngon, giá cả...)</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-3 fs-5 shadow-sm"><i
                                            class="bi bi-cloud-arrow-up-fill me-2"></i> Lưu Lại Ngay</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info shadow-sm sticky-top mt-3" style="top: 90px;">
                        <h5 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>Thông báo</h5>
                        <p class="mb-2">Bạn đang ở chế độ <b>Xem</b>. Vui lòng <a href="login.php"
                                class="fw-bold text-primary">Đăng nhập Admin</a> để thêm hoặc chỉnh sửa địa điểm.</p>
                        <hr class="my-2">
                        <p class="mb-0 text-warning fw-semibold">🔔 Lưu ý: Vui lòng lọc xong mới quay random nếu dùng!</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-8">
                <div
                    class="filter-bar d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 fw-bold text-dark text-nowrap">Danh sách (<?= count($places) ?>)</h5>

                        <?php if (!empty($places)): ?>
                            <button class="btn btn-warning btn-sm fw-bold shadow-sm rounded-pill px-3 py-2 text-dark"
                                data-bs-toggle="modal" data-bs-target="#wheelModal">
                                <i class="bi bi-compass-fill me-1"></i> Random
                            </button>
                        <?php endif; ?>

                        <button class="btn btn-success btn-sm fw-bold shadow-sm rounded-pill px-3 py-2 text-white"
                            onclick="getNearbyPlaces(this)">
                            <i class="bi bi-geo-fill me-1"></i> Gần tôi
                        </button>

                        <?php if (!empty($filterCity) || !empty($filterDistrict) || !empty($filterCategory) || !empty($search) || $sortDistance): ?>
                            <a href="index.php" class="badge bg-danger text-decoration-none rounded-pill px-3 py-2"><i
                                    class="bi bi-x-lg me-1"></i> Xóa lọc</a>
                        <?php endif; ?>
                    </div>

                    <form method="GET" class="d-flex flex-column flex-sm-row gap-2 flex-grow-1 justify-content-end">
                        <div class="input-group input-group-sm flex-nowrap" style="min-width: 200px;">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Tìm tên quán, địa chỉ..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="input-group input-group-sm flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-building"></i></span>
                            <select name="filter_city" id="filter_city"
                                class="form-select form-select-sm border-start-0 ps-0" style="min-width: 100px;"
                                onchange="updateDistricts('filter_city', 'filter_district'); this.form.submit()">
                                <option value="">Tất cả TP</option>
                                <?php foreach (array_keys($locations) as $city): ?>
                                    <option value="<?= $city ?>" <?= ($filterCity == $city) ? 'selected' : '' ?>>
                                        <?= $city ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group input-group-sm flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-geo-alt-fill"></i></span>
                            <select name="filter_district" id="filter_district"
                                class="form-select form-select-sm border-start-0 ps-0" style="min-width: 100px;"
                                onchange="this.form.submit()">
                                <option value="">Tất cả Quận</option>
                            </select>
                        </div>
                        <div class="input-group input-group-sm flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-tags-fill"></i></span>
                            <select name="filter_category" class="form-select form-select-sm border-start-0 ps-0"
                                onchange="this.form.submit()" style="min-width: 100px;">
                                <option value="">Tất cả Danh mục</option>
                                <?php foreach ($cats as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= ($filterCategory == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" hidden></button>
                    </form>
                </div>

                <div class="row g-4">
                    <?php foreach ($places as $place): ?>
                        <?php
                        $clickLink = !empty($place['original_link']) ? $place['original_link'] : "#";
                        if ($clickLink === "#" && $place['latitude']) {
                            $clickLink = "http://maps.google.com/?q=" . $place['latitude'] . "," . $place['longitude'];
                        }

                        $imageArray = [];
                        if (!empty($place['images'])) {
                            $imageArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $place['images']))));
                        }
                        $hasImages = count($imageArray) > 0;
                        ?>
                        <div class="col-md-6 col-xl-6">
                            <div class="card place-card h-100" data-lat="<?= $place['latitude'] ?>"
                                data-lng="<?= $place['longitude'] ?>">

                                <?php if ($hasImages): $thumbnail = $imageArray[0]; ?>
                                    <div class="thumbnail-wrapper position-relative"
                                        style="height: 200px; width: 100%; overflow: hidden; border-bottom: 1px solid #eee; background: #000;">
                                        <a href="<?= htmlspecialchars($thumbnail) ?>"
                                            data-fancybox="gallery-<?= $place['id'] ?>"
                                            data-caption="<?= htmlspecialchars($place['name']) ?>" class="d-block h-100">
                                            <img src="<?= htmlspecialchars($thumbnail) ?>" class="place-thumbnail"
                                                style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;"
                                                alt="<?= htmlspecialchars($place['name']) ?>" loading="lazy" width="400"
                                                height="200">
                                            <?php if (count($imageArray) > 1): ?>
                                                <div
                                                    class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 fs-6 p-2 shadow-sm border border-secondary">
                                                    <i class="bi bi-images me-1"></i> +<?= count($imageArray) - 1 ?>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        <?php for ($i = 1; $i < count($imageArray); $i++): ?>
                                            <a href="<?= htmlspecialchars($imageArray[$i]) ?>"
                                                data-fancybox="gallery-<?= $place['id'] ?>"
                                                data-caption="<?= htmlspecialchars($place['name']) ?>" style="display: none;"></a>
                                        <?php endfor; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 200px; width: 100%; border-bottom: 1px solid #eee;">
                                        <div class="text-center text-muted opacity-50">
                                            <i class="bi bi-image fs-1 d-block mb-2"></i>
                                            <small>Chưa có ảnh</small>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="place-card-body d-flex flex-column h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span
                                                class="badge bg-info mb-2"><?= htmlspecialchars($place['category_name'] ?? 'Khác') ?></span>
                                            <span
                                                class="badge bg-light text-dark border ms-1"><?= htmlspecialchars(($place['city'] == 'Hồ Chí Minh' ? 'HCM' : $place['city']) . ' - ' . $place['district']) ?></span>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="text-warning small mb-1">
                                                <?= str_repeat('<i class="bi bi-star-fill"></i>', $place['rating']) ?>
                                            </div>
                                            <?php if ($isAdmin): ?>
                                                <div class="d-flex gap-1 mt-1">
                                                    <button class="btn btn-sm btn-light border text-primary px-2 py-0"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        onclick="fillEditModal(<?= htmlspecialchars(json_encode($place)) ?>)"
                                                        title="Sửa"><i class="bi bi-pencil-fill"></i></button>
                                                    <a href="index.php?action=delete&id=<?= $place['id'] ?>"
                                                        class="btn btn-sm btn-light border text-danger px-2 py-0"
                                                        onclick="return confirm('Bạn chắc chắn muốn xóa?');" title="Xóa"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <h5 class="place-title fw-bold text-truncate"
                                        title="<?= htmlspecialchars($place['name']) ?>">
                                        <?= htmlspecialchars($place['name']) ?></h5>

                                    <?php if (isset($place['distance'])): ?>
                                        <div class="mb-2">
                                            <span class="badge bg-secondary shadow-sm p-2 distance-badge loading">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span> Đang tính đường đi...
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <p class="place-address mb-2 text-truncate"
                                        title="<?= htmlspecialchars($place['address']) ?>"><i
                                            class="bi bi-geo-alt-fill text-danger mt-1 flex-shrink-0"></i> <span
                                            class="text-truncate"><?= htmlspecialchars($place['address'] ?: 'Chưa cập nhật địa chỉ') ?></span>
                                    </p>

                                    <?php if (!empty($place['description'])): ?>
                                        <div class="place-note"><i
                                                class="bi bi-quote me-1 opacity-50"></i><?= htmlspecialchars($place['description']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-auto pt-3 d-flex gap-2">
                                        <a href="<?= htmlspecialchars($clickLink) ?>" target="_blank"
                                            class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-map-fill"></i>
                                            Map</a>
                                        <?php if ($hasImages): ?>
                                            <button class="btn btn-outline-success w-100 fw-bold"
                                                onclick="document.querySelector('[data-fancybox=\'gallery-<?= $place['id'] ?>\']').click();"><i
                                                    class="bi bi-zoom-in"></i> Zoom</button>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary w-100 fw-bold opacity-50" disabled><i
                                                    class="bi bi-image"></i> 0 ảnh</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($places)): ?>
                        <div class="col-12">
                            <div class="alert alert-light text-center p-5 shadow-sm rounded-4"><i
                                    class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>Chưa có địa điểm nào phù hợp.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="catModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Quản lý Danh mục</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary fw-bold mb-3">Thêm mới</h6>
                    <form method="POST" class="d-flex gap-2 mb-4 pb-4 border-bottom"><input type="hidden" name="action"
                            value="add_category"><input type="text" name="category_name" class="form-control" required
                            placeholder="VD: Trà sữa..."><button type="submit"
                            class="btn btn-primary text-nowrap px-4">Thêm</button></form>
                    <h6 class="text-dark fw-bold mb-3">Danh sách hiện tại</h6>
                    <div style="max-height: 300px; overflow-y: auto;" class="pe-2"><?php foreach ($cats as $cat): ?>
                            <div class="d-flex gap-2 align-items-center mb-2 cat-row p-2 border rounded-3 bg-light">
                                <form method="POST" class="d-flex gap-2 flex-grow-1"><input type="hidden" name="action"
                                        value="update_category"><input type="hidden" name="cat_id"
                                        value="<?= $cat['id'] ?>"><input type="text" name="cat_name"
                                        class="form-control form-control-sm bg-white"
                                        value="<?= htmlspecialchars($cat['name']) ?>"><button type="submit"
                                        class="btn btn-sm btn-success px-3"><i class="bi bi-check-lg"></i></button></form>
                                <form method="POST" onsubmit="return confirm('Xoá danh mục này?');"><input type="hidden"
                                        name="action" value="delete_category"><input type="hidden" name="cat_id"
                                        value="<?= $cat['id'] ?>"><button type="submit"
                                        class="btn btn-sm btn-outline-danger px-3"><i class="bi bi-trash"></i></button>
                                </form>
                            </div><?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="locModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-map-fill me-2"></i>Quản lý Khu vực</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-dark fw-bold mb-2">1. Thêm Thành phố mới</h6>
                    <form method="POST" class="d-flex gap-2 mb-4 pb-4 border-bottom">
                        <input type="hidden" name="action" value="add_city">
                        <input type="text" name="city_name" class="form-control" required
                            placeholder="Nhập tên TP (VD: Đà Lạt)...">
                        <button type="submit" class="btn btn-success text-nowrap px-3 fw-bold">Thêm</button>
                    </form>
                    <h6 class="text-dark fw-bold mb-2">2. Thêm Quận/Huyện vào Thành phố</h6>
                    <form method="POST" class="row g-2 align-items-center">
                        <input type="hidden" name="action" value="add_district">
                        <div class="col-5">
                            <select name="city_id" class="form-select" required>
                                <option value="">-- Chọn TP --</option>
                                <?php foreach ($dbCities as $c): ?><option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <input type="text" name="district_name" class="form-control" required
                                placeholder="Tên Quận/Huyện...">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold"><i
                                    class="bi bi-plus-lg"></i></button>
                        </div>
                    </form>
                    <div class="mt-3 text-muted small fst-italic">* Lưu ý: Sau khi thêm, trang sẽ tải lại để cập nhật dữ
                        liệu.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="edit_place">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="current_lat" id="edit_lat">
                        <input type="hidden" name="current_lng" id="edit_lng">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating"><input type="text" name="name" id="edit_name"
                                        class="form-control" required><label>Tên địa điểm</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="category_id" id="edit_cat" class="form-select">
                                        <?php foreach ($cats as $cat): ?><option value="<?= $cat['id'] ?>">
                                                <?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
                                    </select><label>Danh mục</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="city" id="edit_city" class="form-select"
                                    onchange="updateDistricts('edit_city', 'edit_district')">
                                    <?php foreach (array_keys($locations) as $city): ?><option value="<?= $city ?>">
                                            <?= $city ?></option><?php endforeach; ?>
                                </select><label>Thành phố</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="district" id="edit_district" class="form-select">
                                    <option value="">-- Chọn --</option>
                                </select><label>Quận / Huyện</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="rating" id="edit_rating" class="form-select">
                                    <option value="5">5 - Tuyệt vời</option>
                                    <option value="4">4 - Ngon</option>
                                    <option value="3">3 - Ổn</option>
                                    <option value="2">2 - Tệ</option>
                                    <option value="1">1 - Rất tệ</option>
                                </select><label>Đánh giá</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating"><input type="url" name="map_url" id="edit_map_url"
                                    class="form-control"><label class="text-danger">Link Google Maps (Dán đè lên nếu
                                    muốn đổi)</label></div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating"><input type="text" name="address" id="edit_address"
                                    class="form-control"><label>Địa chỉ hiển thị</label></div>
                        </div>

                        <div class="col-12" id="edit_old_images_container"></div>
                        <div class="col-12 bg-light p-3 rounded border">
                            <label class="form-label fw-bold text-success"><i class="bi bi-upload"></i> Tải thêm ảnh
                                mới</label>
                            <input type="file" name="upload_images[]" class="form-control" multiple accept="image/*">
                        </div>

                        <div class="col-12">
                            <div class="form-floating"><textarea name="description" id="edit_desc" class="form-control"
                                    style="height: 100px"></textarea><label>Ghi chú</label></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light"><button type="button" class="btn btn-light border"
                            data-bs-dismiss="modal">Hủy</button><button type="submit"
                            class="btn btn-primary px-4 fw-bold">Cập nhật thay đổi</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="wheelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="bi bi-star-fill me-2"></i>Hôm nay ăn gì?</h5><button
                        type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center overflow-hidden">
                    <div id="wheel-container" style="position: relative; width: 300px; height: 300px; margin: 0 auto;">
                        <canvas id="wheelCanvas" width="300" height="300"></canvas>
                        <div id="wheel-pointer"
                            style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-top: 25px solid #ff4757; z-index: 10;">
                        </div>
                    </div>
                    <h4 id="result-name" class="mt-4 fw-bold text-primary"></h4>
                    <p id="result-address" class="text-muted small"></p>
                </div>
                <div class="modal-footer"><button type="button" id="spinBtn"
                        class="btn btn-primary w-100 py-2 fs-5 fw-bold">QUAY NGAY</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <script>
        Fancybox.bind('[data-fancybox]', {
            Images: {
                zoom: true
            },
            Thumbs: {
                type: "modern"
            }
        });

        const locationsData = <?php echo json_encode($locations); ?>;
        const currentPlaces = <?php echo json_encode($places); ?>;

        function updateDistricts(citySelectId, districtSelectId, selectedDistrict = null) {
            const citySel = document.getElementById(citySelectId);
            const distSel = document.getElementById(districtSelectId);
            const city = citySel.value;
            distSel.innerHTML = '<option value="">-- Tất cả/Chọn --</option>';
            if (city && locationsData[city]) {
                locationsData[city].forEach(function(d) {
                    const option = document.createElement("option");
                    option.value = d;
                    option.text = d;
                    if (selectedDistrict && d === selectedDistrict) option.selected = true;
                    distSel.appendChild(option);
                });
            }
        }

        function fillEditModal(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_address').value = data.address;
            document.getElementById('edit_desc').value = data.description;
            document.getElementById('edit_rating').value = data.rating;
            document.getElementById('edit_cat').value = data.category_id;
            document.getElementById('edit_lat').value = data.latitude;
            document.getElementById('edit_lng').value = data.longitude;
            document.getElementById('edit_map_url').value = data.original_link || '';
            document.getElementById('edit_city').value = data.city || 'Hồ Chí Minh';
            updateDistricts('edit_city', 'edit_district', data.district);

            const imagesStr = data.images || '';
            const imageList = imagesStr.split('\n').filter(i => i.trim() !== '');
            const container = document.getElementById('edit_old_images_container');
            container.innerHTML = '';

            if (imageList.length > 0) {
                let html =
                    '<label class="form-label fw-bold text-secondary">Ảnh đang có (Bỏ tick để xóa):</label><div class="d-flex flex-wrap gap-2 mb-2">';
                imageList.forEach(img => {
                    html += `
                <div class="position-relative border rounded p-1 bg-white text-center shadow-sm" style="width: 80px;">
                    <img src="${img}" style="width: 100%; height: 60px; object-fit: cover; border-radius: 4px;">
                    <div class="form-check mt-1 d-inline-block">
                        <input class="form-check-input float-none" type="checkbox" name="keep_images[]" value="${img}" checked title="Giữ lại ảnh này">
                    </div>
                </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('add_city') && (document.getElementById('add_city').value = 'Hồ Chí Minh',
                updateDistricts('add_city', 'add_district'));
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('filter_city')) {
                updateDistricts('filter_city', 'filter_district', urlParams.get('filter_district'));
            }
        });

        const canvas = document.getElementById('wheelCanvas');
        const ctx = canvas.getContext('2d');
        const spinBtn = document.getElementById('spinBtn');
        const resultName = document.getElementById('result-name');
        const resultAddress = document.getElementById('result-address');

        let startAngle = 0;
        let spinTimeout = null;
        let spinAngleStart = 10;
        let spinTime = 0;
        let spinTimeTotal = 0;

        function drawWheel() {
            if (!currentPlaces.length) return;
            const centerX = 150,
                centerY = 150,
                radius = 140;
            const arc = Math.PI / (currentPlaces.length / 2);

            ctx.clearRect(0, 0, 300, 300);
            currentPlaces.forEach((place, i) => {
                const angle = startAngle + i * arc;
                ctx.fillStyle = i % 2 === 0 ? '#FF7F50' : '#20c997';
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, angle, angle + arc, false);
                ctx.lineTo(centerX, centerY);
                ctx.fill();

                ctx.save();
                ctx.fillStyle = "white";
                ctx.translate(centerX + Math.cos(angle + arc / 2) * radius * 0.6, centerY + Math.sin(angle + arc /
                    2) * radius * 0.6);
                ctx.rotate(angle + arc / 2 + Math.PI / 2);
                const text = place.name.substring(0, 15);
                ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
                ctx.restore();
            });
        }

        function rotateWheel() {
            spinTime += 30;
            if (spinTime >= spinTimeTotal) {
                stopRotateWheel();
                return;
            }
            const spinAngle = spinAngleStart - easeOut(spinTime, 0, spinAngleStart, spinTimeTotal);
            startAngle += (spinAngle * Math.PI / 180);
            drawWheel();
            spinTimeout = setTimeout(rotateWheel, 30);
        }

        function stopRotateWheel() {
            clearTimeout(spinTimeout);
            const arc = Math.PI / (currentPlaces.length / 2);
            const degrees = startAngle * 180 / Math.PI + 90;
            const arcd = arc * 180 / Math.PI;
            const index = Math.floor((360 - degrees % 360) / arcd) % currentPlaces.length;

            const winner = currentPlaces[index];
            resultName.innerText = "⭐ " + winner.name;
            resultAddress.innerText = winner.address;
            spinBtn.disabled = false;
            spinBtn.innerText = "QUAY LẠI";
        }

        function easeOut(t, b, c, d) {
            const ts = (t /= d) * t;
            const tc = ts * t;
            return b + c * (tc + -3 * ts + 3 * t);
        }

        spinBtn.addEventListener('click', () => {
            resultName.innerText = "Đang quay...";
            resultAddress.innerText = "";
            spinBtn.disabled = true;
            spinAngleStart = Math.random() * 10 + 10;
            spinTime = 0;
            spinTimeTotal = Math.random() * 3 + 4 * 1000;
            rotateWheel();
        });

        document.getElementById('wheelModal').addEventListener('shown.bs.modal', function() {
            drawWheel();
        });

        // Cập nhật lại hàm này ở cuối file index.php
        function getNearbyPlaces(btn) {
            if (navigator.geolocation) {
                // Lưu lại giao diện cũ của nút và tạo hiệu ứng loading
                const originalText = btn.innerHTML;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang định vị...';
                btn.disabled = true; // Khóa nút tránh click nhiều lần

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Lấy thành công
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        window.location.href = `index.php?user_lat=${lat}&user_lng=${lng}&sort=distance`;
                    },
                    function(error) {
                        // Thất bại: Báo lỗi chi tiết
                        console.warn(`Lỗi định vị (${error.code}): ${error.message}`);
                        let errorMsg = "Không thể lấy vị trí. ";

                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg += "Bạn đã từ chối cấp quyền định vị cho trang web này.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg += "Không có tín hiệu GPS hoặc mạng.";
                                break;
                            case error.TIMEOUT:
                                errorMsg += "Hết thời gian chờ lấy vị trí.";
                                break;
                            default:
                                errorMsg += "Lỗi không xác định.";
                                break;
                        }
                        alert(errorMsg);

                        // Khôi phục lại nút bấm
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, {
                        enableHighAccuracy: true, // Ưu tiên độ chính xác cao
                        timeout: 10000, // Chờ tối đa 10 giây
                        maximumAge: 0 // Không dùng cache cũ
                    }
                );
            } else {
                alert("Trình duyệt của bạn không hỗ trợ định vị (Geolocation).");
            }
        }

        // --- LẤY TỌA ĐỘ NGƯỜI DÙNG TỪ URL ---
        const userLat = parseFloat(<?= json_encode($userLat) ?>);
        const userLng = parseFloat(<?= json_encode($userLng) ?>);

        // Hàm tính khoảng cách đường thẳng (Haversine) - Tốc độ tức thì
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Bán kính trái đất (km)
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return (R * c).toFixed(1); // Làm tròn 1 chữ số thập phân
        }

        // --- HIỂN THỊ KHOẢNG CÁCH VÀ TẠO LINK CHỈ ĐƯỜNG GOOGLE MAPS ---
        document.addEventListener("DOMContentLoaded", function() {
            if (userLat && userLng && !isNaN(userLat) && !isNaN(userLng)) {
                const cards = document.querySelectorAll('.place-card');

                cards.forEach(card => {
                    const placeLat = parseFloat(card.getAttribute('data-lat'));
                    const placeLng = parseFloat(card.getAttribute('data-lng'));
                    const badge = card.querySelector('.distance-badge');

                    // Nếu có tọa độ hợp lệ
                    if (!isNaN(placeLat) && !isNaN(placeLng)) {
                        // 1. Hiển thị khoảng cách tức thì
                        if (badge) {
                            const distanceKm = calculateDistance(userLat, userLng, placeLat, placeLng);
                            badge.className = 'badge bg-success shadow-sm p-2 distance-badge';
                            badge.innerHTML =
                                `<i class="bi bi-geo-alt-fill me-1"></i> Cách đây ~${distanceKm} km`;
                        }

                        // 2. Chuyển đổi nút "Map" thành nút "Chỉ đường" bằng Google Maps
                        const mapBtn = card.querySelector('a.btn-outline-danger');
                        if (mapBtn) {
                            // URL scheme chuẩn của Google Maps Directions
                            const dirUrl =
                                `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${placeLat},${placeLng}`;
                            mapBtn.href = dirUrl;
                            mapBtn.innerHTML = `<i class="bi bi-signpost-split-fill"></i> Chỉ đường`;
                            mapBtn.target = "_blank"; // Mở tab mới hoặc mở app Google Maps trên điện thoại
                        }
                    } else if (badge) {
                        // Báo lỗi nếu quán ăn chưa có tọa độ chuẩn
                        badge.className = 'badge bg-secondary shadow-sm p-2 distance-badge';
                        badge.innerHTML = `<i class="bi bi-exclamation-circle"></i> Chưa rõ tọa độ`;
                    }
                });
            }
        });
    </script>
</body>

</html>