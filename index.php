<?php
require 'db.php';

// --- CẤU TRÚC DỮ LIỆU: THÀNH PHỐ => QUẬN/HUYỆN ---
$locations = [
    'Hồ Chí Minh' => [
        'Quận 1',
        'Quận 3',
        'Quận 4',
        'Quận 5',
        'Quận 6',
        'Quận 7',
        'Quận 8',
        'Quận 9',
        'Quận 10',
        'Quận 11',
        'Quận 12',
        'Bình Thạnh',
        'Gò Vấp',
        'Phú Nhuận',
        'Tân Bình',
        'Tân Phú',
        'Bình Tân',
        'TP. Thủ Đức',
        'Huyện Bình Chánh',
        'Huyện Hóc Môn',
        'Huyện Nhà Bè',
        'Huyện Củ Chi',
        'Huyện Cần Giờ'
    ],
    'Bảo Lộc' => [],
    'Vũng Tàu' => []
];

// --- HÀM HỖ TRỢ ---
function getCoordinatesFromUrl($url)
{
    preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches);
    if (isset($matches[1]) && isset($matches[2])) {
        return ['lat' => $matches[1], 'lng' => $matches[2]];
    }
    return null;
}

// --- XỬ LÝ POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Thêm Danh mục
    if (isset($_POST['action']) && $_POST['action'] == 'add_category') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([trim($_POST['category_name'])]);
        header("Location: index.php");
        exit;
    }
    // 2. Sửa Danh mục
    if (isset($_POST['action']) && $_POST['action'] == 'update_category') {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([trim($_POST['cat_name']), $_POST['cat_id']]);
        header("Location: index.php");
        exit;
    }
    // 3. Xóa Danh mục
    if (isset($_POST['action']) && $_POST['action'] == 'delete_category') {
        $catId = $_POST['cat_id'];
        $pdo->prepare("UPDATE places SET category_id = NULL WHERE category_id = ?")->execute([$catId]);
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$catId]);
        header("Location: index.php");
        exit;
    }
    // 4. Thêm Địa điểm
    if (isset($_POST['action']) && $_POST['action'] == 'add_place') {
        $lat = null;
        $lng = null;
        $originalLink = $_POST['map_url'];

        if (!empty($originalLink)) {
            $coords = getCoordinatesFromUrl($originalLink);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        // Thêm city vào câu lệnh INSERT
        $sql = "INSERT INTO places (name, category_id, city, district, address, description, latitude, longitude, rating, original_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$_POST['name'], $_POST['category_id'], $_POST['city'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating'], $originalLink]);
        header("Location: index.php");
        exit;
    }
    // 5. Sửa Địa điểm
    if (isset($_POST['action']) && $_POST['action'] == 'edit_place') {
        $lat = $_POST['current_lat'];
        $lng = $_POST['current_lng'];
        $originalLink = $_POST['map_url'];

        if (!empty($originalLink)) {
            $coords = getCoordinatesFromUrl($originalLink);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        // Thêm city vào câu lệnh UPDATE
        $sql = "UPDATE places SET name=?, category_id=?, city=?, district=?, address=?, description=?, latitude=?, longitude=?, rating=?, original_link=? WHERE id=?";
        $pdo->prepare($sql)->execute([$_POST['name'], $_POST['category_id'], $_POST['city'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating'], $originalLink, $_POST['id']]);
        header("Location: index.php");
        exit;
    }
}

// --- XỬ LÝ GET (XÓA) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM places WHERE id = ?")->execute([$_GET['id']]);
    header("Location: index.php");
    exit;
}

// --- LẤY DỮ LIỆU ---
$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Query Lọc
$sqlPlace = "SELECT places.*, categories.name as category_name FROM places LEFT JOIN categories ON places.category_id = categories.id WHERE 1=1";
$params = [];
$filterCity = $_GET['filter_city'] ?? '';
$filterDistrict = $_GET['filter_district'] ?? '';
$filterCategory = $_GET['filter_category'] ?? '';

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

$sqlPlace .= " ORDER BY places.created_at DESC";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-journal-richtext text-secondary me-2 fs-3"></i> Địa điểm của Thành Đạt
            </a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row g-4">

            <div class="col-lg-4">
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
                            <form method="POST">
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
                                                <?php foreach (array_keys($locations) as $city): ?>
                                                <option value="<?= $city ?>"><?= $city ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label>Thành phố</label>
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
                                    <label for="floatingAddress">Địa chỉ chi tiết (Số nhà, đường)</label>
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

                                <button type="submit" class="btn btn-primary w-100 py-3 fs-5 shadow-sm">
                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> Lưu Lại Ngay
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div
                    class="filter-bar d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 fw-bold text-dark me-3">Danh sách (<?= count($places) ?>)</h5>
                        <?php if (!empty($filterCity) || !empty($filterDistrict) || !empty($filterCategory)): ?>
                        <a href="index.php" class="badge bg-danger text-decoration-none rounded-pill px-3 py-2"><i
                                class="bi bi-x-lg me-1"></i> Xóa lọc</a>
                        <?php endif; ?>
                    </div>

                    <form method="GET"
                        class="d-flex flex-column flex-md-row gap-2 flex-grow-1 flex-md-grow-0 align-items-md-center justify-content-end">
                        <div class="input-group input-group-sm flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-building"></i></span>
                            <select name="filter_city" id="filter_city"
                                class="form-select form-select-sm border-start-0 ps-0" style="min-width: 110px;"
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
                                class="form-select form-select-sm border-start-0 ps-0" style="min-width: 110px;"
                                onchange="this.form.submit()">
                                <option value="">Tất cả Quận</option>
                            </select>
                        </div>

                        <div class="input-group input-group-sm flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 text-secondary"><i
                                    class="bi bi-tags-fill"></i></span>
                            <select name="filter_category" class="form-select form-select-sm border-start-0 ps-0"
                                onchange="this.form.submit()" style="min-width: 110px;">
                                <option value="">Tất cả Danh mục</option>
                                <?php foreach ($cats as $cat): ?><option value="<?= $cat['id'] ?>"
                                    <?= ($filterCategory == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="row g-4">
                    <?php foreach ($places as $place): ?>
                    <?php
                        $clickLink = !empty($place['original_link']) ? $place['original_link'] : "#";
                        if ($clickLink === "#" && $place['latitude']) {
                            $clickLink = "http://maps.google.com/?q=" . $place['latitude'] . "," . $place['longitude'];
                        }
                        ?>
                    <div class="col-md-6 col-xl-6">
                        <div class="card place-card h-100">
                            <div class="action-buttons">
                                <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                                    onclick="fillEditModal(<?= htmlspecialchars(json_encode($place)) ?>)"><i
                                        class="bi bi-pencil-fill"></i></button>
                                <a href="index.php?action=delete&id=<?= $place['id'] ?>" class="btn-action btn-delete"
                                    onclick="return confirm('Bạn chắc chắn muốn xóa?');"><i
                                        class="bi bi-trash-fill"></i></a>
                            </div>

                            <div class="card-map-header">
                                <?php if ($place['latitude']): ?>
                                <iframe class="map-iframe" style="pointer-events: none;" loading="lazy"
                                    src="https://maps.google.com/maps?q=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>&hl=vi&z=16&output=embed"></iframe>
                                <?php else: ?>
                                <div class="no-map-placeholder">
                                    <i class="bi bi-map-fill fs-1 mb-2 opacity-50"></i>
                                    <span>Chưa có bản đồ</span>
                                </div>
                                <?php endif; ?>
                                <a href="<?= htmlspecialchars($clickLink) ?>" target="_blank"
                                    class="stretched-link"></a>
                            </div>

                            <div class="place-card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span
                                            class="badge bg-info mb-2"><?= htmlspecialchars($place['category_name'] ?? 'Khác') ?></span>
                                        <span class="badge bg-light text-dark border ms-1">
                                            <?= htmlspecialchars(($place['city'] == 'Hồ Chí Minh' ? 'HCM' : $place['city']) . ' - ' . $place['district']) ?>
                                        </span>
                                    </div>
                                    <div class="text-warning small">
                                        <?= str_repeat('<i class="bi bi-star-fill"></i>', $place['rating']) ?>
                                    </div>
                                </div>

                                <h5 class="place-title fw-bold text-truncate"><?= htmlspecialchars($place['name']) ?>
                                </h5>

                                <p class="place-address mb-2 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger mt-1 flex-shrink-0"></i>
                                    <span
                                        class="text-truncate"><?= htmlspecialchars($place['address'] ?: 'Chưa cập nhật địa chỉ') ?></span>
                                </p>

                                <?php if (!empty($place['description'])): ?>
                                <div class="place-note mt-auto">
                                    <i class="bi bi-quote me-1 opacity-50"></i>
                                    <?= htmlspecialchars($place['description']) ?>
                                </div>
                                <?php else: ?>
                                <div class="mt-auto"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($places)): ?>
                    <div class="col-12">
                        <div class="alert alert-light text-center p-5 shadow-sm rounded-4">
                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                            Chưa có địa điểm nào phù hợp.
                        </div>
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
                            placeholder="VD: Trà sữa, Ăn vặt..."><button type="submit"
                            class="btn btn-primary text-nowrap px-4">Thêm</button></form>
                    <h6 class="text-dark fw-bold mb-3">Danh sách hiện tại</h6>
                    <div style="max-height: 300px; overflow-y: auto;" class="pe-2"><?php foreach ($cats as $cat): ?><div
                            class="d-flex gap-2 align-items-center mb-2 cat-row p-2 border rounded-3 bg-light">
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
                        </div><?php endforeach; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Chỉnh sửa thông tin</h5><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4"><input type="hidden" name="action" value="edit_place"><input
                            type="hidden" name="id" id="edit_id"><input type="hidden" name="current_lat"
                            id="edit_lat"><input type="hidden" name="current_lng" id="edit_lng">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating"><input type="text" name="name" id="edit_name"
                                        class="form-control" required placeholder="Tên"><label>Tên địa điểm</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><select name="category_id" id="edit_cat"
                                        class="form-select"><?php foreach ($cats as $cat): ?><option
                                            value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?></select><label>Danh mục</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><select name="city" id="edit_city" class="form-select"
                                        onchange="updateDistricts('edit_city', 'edit_district')"><?php foreach (array_keys($locations) as $city): ?>
                                        <option value="<?= $city ?>"><?= $city ?></option>
                                        <?php endforeach; ?>
                                    </select><label>Thành phố</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><select name="district" id="edit_district"
                                        class="form-select">
                                        <option value="">-- Chọn --</option>
                                    </select><label>Quận / Huyện</label></div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating"><select name="rating" id="edit_rating" class="form-select">
                                        <option value="5">5 - Tuyệt vời</option>
                                        <option value="4">4 - Ngon</option>
                                        <option value="3">3 - Ổn</option>
                                        <option value="2">2 - Tệ</option>
                                        <option value="1">1 - Rất tệ</option>
                                    </select><label>Đánh giá</label></div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating"><input type="url" name="map_url" id="edit_map_url"
                                        class="form-control" placeholder="Link"><label class="text-danger">Link Google
                                        Maps (Dán đè lên nếu muốn đổi)</label></div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating"><input type="text" name="address" id="edit_address"
                                        class="form-control" placeholder="Địa chỉ"><label>Địa chỉ hiển thị</label></div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating"><textarea name="description" id="edit_desc"
                                        class="form-control" style="height: 100px"
                                        placeholder="Ghi chú"></textarea><label>Ghi chú</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light"><button type="button" class="btn btn-light border"
                            data-bs-dismiss="modal">Hủy</button><button type="submit"
                            class="btn btn-primary px-4 fw-bold">Cập nhật thay đổi</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // 1. Dữ liệu từ PHP -> JS
    const locationsData = <?php echo json_encode($locations); ?>;

    // 2. Hàm cập nhật Quận dựa theo Thành phố
    function updateDistricts(citySelectId, districtSelectId, selectedDistrict = null) {
        const citySel = document.getElementById(citySelectId);
        const distSel = document.getElementById(districtSelectId);
        const city = citySel.value;

        // Xóa cũ
        distSel.innerHTML = '<option value="">-- Tất cả/Chọn --</option>';

        if (city && locationsData[city]) {
            locationsData[city].forEach(function(d) {
                const option = document.createElement("option");
                option.value = d;
                option.text = d;
                if (selectedDistrict && d === selectedDistrict) {
                    option.selected = true;
                }
                distSel.appendChild(option);
            });
        }
    }

    // 3. Hàm điền dữ liệu vào Modal Sửa
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

        // Xử lý City & District
        const cityVal = data.city || 'Hồ Chí Minh'; // Mặc định HCM nếu null
        document.getElementById('edit_city').value = cityVal;

        // Trigger cập nhật district list rồi mới chọn district
        updateDistricts('edit_city', 'edit_district', data.district);
    }

    // 4. Khởi chạy mặc định khi load trang
    document.addEventListener("DOMContentLoaded", function() {
        // Form thêm mới: Mặc định chọn HCM
        document.getElementById('add_city').value = 'Hồ Chí Minh';
        updateDistricts('add_city', 'add_district');

        // Bộ lọc: Nếu URL có sẵn city, hãy fill district tương ứng
        const urlParams = new URLSearchParams(window.location.search);
        const filterCity = urlParams.get('filter_city');
        const filterDistrict = urlParams.get('filter_district');
        if (filterCity) {
            updateDistricts('filter_city', 'filter_district', filterDistrict);
        }
    });
    </script>
</body>

</html>