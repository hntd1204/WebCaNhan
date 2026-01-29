<?php
require 'db.php';

// --- DANH SÁCH QUẬN (TP.HCM) ---
$districts = [
    'Quận 1',
    'Quận 3',
    'Quận 4',
    'Quận 5',
    'Quận 6',
    'Quận 7',
    'Quận 8',
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
    'Huyện Cần Giờ',
    'Khác'
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

// --- XỬ LÝ POST (THÊM / SỬA / XÓA) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Thêm Danh mục
    if (isset($_POST['action']) && $_POST['action'] == 'add_category') {
        $newCatName = trim($_POST['category_name']);
        if (!empty($newCatName)) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt->execute([$newCatName]);
        }
        header("Location: index.php");
        exit;
    }

    // 2. Cập nhật tên Danh mục
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
        if (!empty($_POST['map_url'])) {
            $coords = getCoordinatesFromUrl($_POST['map_url']);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        $sql = "INSERT INTO places (name, category_id, district, address, description, latitude, longitude, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['name'], $_POST['category_id'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating']]);
        header("Location: index.php");
        exit;
    }

    // 5. Sửa Địa điểm
    if (isset($_POST['action']) && $_POST['action'] == 'edit_place') {
        $id = $_POST['id'];
        $mapUrl = $_POST['map_url'];
        $lat = $_POST['current_lat'];
        $lng = $_POST['current_lng'];

        if (!empty($mapUrl) && strpos($mapUrl, '@') !== false) {
            $coords = getCoordinatesFromUrl($mapUrl);
            if ($coords) {
                $lat = $coords['lat'];
                $lng = $coords['lng'];
            }
        }

        $sql = "UPDATE places SET name=?, category_id=?, district=?, address=?, description=?, latitude=?, longitude=?, rating=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['name'], $_POST['category_id'], $_POST['district'], $_POST['address'], $_POST['description'], $lat, $lng, $_POST['rating'], $id]);
        header("Location: index.php");
        exit;
    }
}

// --- XỬ LÝ GET (XÓA & LỌC) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM places WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: index.php");
    exit;
}

// --- LẤY DỮ LIỆU ---
$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ============================================
// LOGIC LỌC DỮ LIỆU (QUẬN + DANH MỤC)
// ============================================
$sqlPlace = "SELECT places.*, categories.name as category_name 
             FROM places 
             LEFT JOIN categories ON places.category_id = categories.id
             WHERE 1=1"; // Kỹ thuật 1=1 để dễ nối chuỗi AND

$params = [];
$filterDistrict = $_GET['filter_district'] ?? '';
$filterCategory = $_GET['filter_category'] ?? '';

// 1. Lọc theo Quận
if (!empty($filterDistrict)) {
    $sqlPlace .= " AND places.district = ?";
    $params[] = $filterDistrict;
}

// 2. Lọc theo Danh mục
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Đồ Của Thành Đạt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
    .cat-row {
        transition: 0.2s;
    }

    .cat-row:hover {
        background-color: #f8f9fa;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark navbar-custom mb-4 sticky-top">
        <div class="container">
            <span class="navbar-brand h1 mb-0"><i class="bi bi-geo-fill"></i> Địa điểm của Thành Đạt</span>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">

            <div class="col-lg-4 mb-4">
                <div class="card card-form sticky-lg-top" style="top: 80px; z-index: 10;">
                    <div class="card-header">
                        <span><i class="bi bi-plus-circle-fill"></i> Check-in Mới</span>
                    </div>
                    <div class="card-body p-3">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_place">

                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted">Tên địa điểm</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="VD: Phở Thìn...">
                            </div>

                            <div class="row mb-2">
                                <div class="col-7">
                                    <label class="form-label small fw-bold text-muted">Danh mục</label>
                                    <div class="input-group">
                                        <select name="category_id" class="form-select">
                                            <?php foreach ($cats as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#catModal" title="Quản lý danh mục">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-bold text-muted">Quận / Huyện</label>
                                    <select name="district" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <?php foreach ($districts as $d): ?>
                                        <option value="<?= $d ?>"><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted">Link Google Maps</label>
                                <input type="url" name="map_url" class="form-control" required
                                    placeholder="Dán link có chứa @...">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted">Địa chỉ hiển thị</label>
                                <input type="text" name="address" class="form-control" placeholder="Số nhà, đường...">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted">Đánh giá</label>
                                <select name="rating" class="form-select">
                                    <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                    <option value="4">⭐⭐⭐⭐ (Ngon)</option>
                                    <option value="3">⭐⭐⭐ (Ổn)</option>
                                    <option value="2">⭐⭐ (Tệ)</option>
                                    <option value="1">⭐ (Rất tệ)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Ghi chú</label>
                                <textarea name="description" class="form-control" rows="2"
                                    placeholder="Note lại món ngon..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-save text-white w-100 rounded-3">
                                <i class="bi bi-save2-fill"></i> Lưu Địa Điểm
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                    <h5 class="text-secondary border-start border-4 border-danger ps-2 mb-0">
                        Danh sách (<?= count($places) ?>)
                    </h5>

                    <form method="GET" class="d-flex align-items-center gap-2">

                        <select name="filter_district" class="form-select form-select-sm" style="width: 140px;"
                            onchange="this.form.submit()">
                            <option value="">📍 Tất cả Quận</option>
                            <?php foreach ($districts as $d): ?>
                            <option value="<?= $d ?>" <?= ($filterDistrict == $d) ? 'selected' : '' ?>>
                                <?= $d ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="filter_category" class="form-select form-select-sm" style="width: 140px;"
                            onchange="this.form.submit()">
                            <option value="">🏷️ Tất cả Danh mục</option>
                            <?php foreach ($cats as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($filterCategory == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (!empty($filterDistrict) || !empty($filterCategory)): ?>
                        <a href="index.php" class="btn btn-sm btn-outline-danger" title="Xóa bộ lọc">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="row g-4">
                    <?php foreach ($places as $place): ?>
                    <div class="col-12">
                        <div class="card place-card p-3">

                            <div class="action-buttons" style="z-index: 10;">
                                <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                                    onclick="fillEditModal(<?= htmlspecialchars(json_encode($place)) ?>)">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <a href="index.php?action=delete&id=<?= $place['id'] ?>" class="btn-action btn-delete"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này không?');">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>

                            <div class="row g-0">
                                <div class="col-md-7 pe-3 d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-info text-dark category-badge">
                                            <?= htmlspecialchars($place['category_name'] ?? 'Chưa phân loại') ?>
                                        </span>
                                        <?php if (!empty($place['district'])): ?>
                                        <span class="badge bg-light text-secondary border ms-1">
                                            <?= htmlspecialchars($place['district']) ?>
                                        </span>
                                        <?php endif; ?>
                                        <span class="text-warning ms-1 small">
                                            <?= str_repeat('<i class="bi bi-star-fill"></i>', $place['rating']) ?>
                                        </span>
                                    </div>

                                    <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($place['name']) ?></h4>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i>
                                        <?= htmlspecialchars($place['address']) ?></p>

                                    <p class="fst-italic bg-light p-2 rounded small text-secondary mb-3">
                                        "<?= htmlspecialchars($place['description']) ?>"
                                    </p>

                                    <div class="mt-auto">
                                        <?php if ($place['latitude']): ?>
                                        <a href="https://www.google.com/maps?q=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>"
                                            target="_blank" class="btn-gmap stretched-link">
                                            <i class="bi bi-google"></i> Xem trên Google Maps
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-5 mt-3 mt-md-0">
                                    <?php if ($place['latitude']): ?>
                                    <div class="map-container shadow-sm position-relative">
                                        <iframe class="map-iframe" style="pointer-events: none;" loading="lazy"
                                            src="https://maps.google.com/maps?q=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>&hl=vi&z=15&output=embed">
                                        </iframe>
                                    </div>
                                    <?php else: ?>
                                    <div
                                        class="map-container d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="bi bi-map-fill me-2"></i> No Map
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($places)): ?>
                    <div class="alert alert-warning text-center">
                        <?php if (!empty($filterDistrict) || !empty($filterCategory)): ?>
                        Không tìm thấy địa điểm nào phù hợp với bộ lọc.
                        <a href="index.php" class="alert-link">Xóa bộ lọc</a>
                        <?php else: ?>
                        Chưa có dữ liệu. Hãy thêm địa điểm đầu tiên!
                        <?php endif; ?>
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
                    <h5 class="modal-title"><i class="bi bi-tags"></i> Quản lý Danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary fw-bold mb-2">Thêm danh mục mới</h6>
                    <form method="POST" class="d-flex gap-2 mb-4 pb-3 border-bottom">
                        <input type="hidden" name="action" value="add_category">
                        <input type="text" name="category_name" class="form-control" required
                            placeholder="Nhập tên danh mục...">
                        <button type="submit" class="btn btn-primary text-nowrap"><i class="bi bi-plus-lg"></i>
                            Thêm</button>
                    </form>

                    <h6 class="text-muted fw-bold mb-2">Danh sách hiện tại</h6>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cats as $cat): ?>
                        <div class="d-flex gap-2 align-items-center mb-2 cat-row p-1 rounded">
                            <form method="POST" class="d-flex gap-2 flex-grow-1">
                                <input type="hidden" name="action" value="update_category">
                                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                <input type="text" name="cat_name" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($cat['name']) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Lưu tên mới"><i
                                        class="bi bi-check-lg"></i></button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Xoá danh mục này?');">
                                <input type="hidden" name="action" value="delete_category">
                                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Đóng</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_place">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="current_lat" id="edit_lat">
                        <input type="hidden" name="current_lng" id="edit_lng">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên địa điểm</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Danh mục</label>
                                <select name="category_id" id="edit_cat" class="form-select">
                                    <?php foreach ($cats as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quận / Huyện</label>
                            <select name="district" id="edit_district" class="form-select">
                                <option value="">-- Chọn --</option>
                                <?php foreach ($districts as $d): ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Google Maps</label>
                            <input type="url" name="map_url" id="edit_map_url" class="form-control"
                                placeholder="Dán link mới để cập nhật toạ độ...">
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="address" id="edit_address" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Đánh giá</label>
                                <select name="rating" id="edit_rating" class="form-select">
                                    <option value="5">⭐⭐⭐⭐⭐</option>
                                    <option value="4">⭐⭐⭐⭐</option>
                                    <option value="3">⭐⭐⭐</option>
                                    <option value="2">⭐⭐</option>
                                    <option value="1">⭐</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function fillEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_address').value = data.address;
        document.getElementById('edit_desc').value = data.description;
        document.getElementById('edit_rating').value = data.rating;
        document.getElementById('edit_cat').value = data.category_id;
        document.getElementById('edit_district').value = data.district || '';
        document.getElementById('edit_lat').value = data.latitude;
        document.getElementById('edit_lng').value = data.longitude;
        document.getElementById('edit_map_url').value = '';
    }
    </script>

</body>

</html>