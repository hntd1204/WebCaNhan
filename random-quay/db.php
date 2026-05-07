<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once __DIR__ . '/../config/database.php';

// Bổ sung cột cấu hình cần thiết. Mỗi lệnh tách riêng để cột nào tồn tại rồi thì bỏ qua cột đó.
$schemaColumns = [
    'settings' => [
        'baucua_multiplier FLOAT DEFAULT 1',
        'blackjack_multiplier FLOAT DEFAULT 2',
        'hilo_multiplier FLOAT DEFAULT 1.2',
        'mines_multiplier FLOAT DEFAULT 1.2',
        'mines_bombs INT DEFAULT 3',
        'minigame_min_bet INT DEFAULT 1000',
        'minigame_max_bet INT DEFAULT 1000000',
        'baucua_max_doors INT DEFAULT 3',
        'mines_cashout_min_steps INT DEFAULT 1',
        'baucua_enabled TINYINT DEFAULT 1',
        'blackjack_enabled TINYINT DEFAULT 1',
        'hilo_enabled TINYINT DEFAULT 1',
        'mines_enabled TINYINT DEFAULT 1'
    ],
    'users' => [
        'baucua_count INT DEFAULT 0',
        'blackjack_count INT DEFAULT 0',
        'hilo_count INT DEFAULT 0',
        'mines_count INT DEFAULT 0',
        'last_reset_date DATE DEFAULT NULL'
    ]
];
foreach ($schemaColumns as $table => $columns) {
    foreach ($columns as $col) {
        try { $pdo->exec("ALTER TABLE `$table` ADD COLUMN $col"); } catch (Exception $e) {}
    }
}
try { $pdo->exec("INSERT IGNORE INTO settings (id) VALUES (1)"); } catch (Exception $e) {}
// --- THÊM LOGIC RESET NHIỆM VỤ QUA NGÀY MỚI ---
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    $uid = $_SESSION['user_id'];
    $today = date('Y-m-d');

    // Kiểm tra xem hôm nay đã reset chưa
    try {
        $stmt = $pdo->prepare("SELECT last_reset_date FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $last_reset = $stmt->fetchColumn();

        if ($last_reset !== $today) {
            // Lấy các mã nhiệm vụ hiện có trong db
            $missions = $pdo->query("SELECT mission_key FROM mission_settings")->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($missions)) {
                $setClause = "";
                foreach ($missions as $key) {
                    if (!preg_match('/^[a-z0-9_]+$/', $key)) continue;
                    $setClause .= "`$key` = 0, "; // Reset số lần chơi về 0
                }
                $setClause .= "last_reset_date = ?"; // Cập nhật ngày reset là hôm nay

                $pdo->prepare("UPDATE users SET $setClause WHERE id = ?")->execute([$today, $uid]);
            } else {
                // Nếu không có nhiệm vụ nào, chỉ cập nhật ngày
                $pdo->prepare("UPDATE users SET last_reset_date = ? WHERE id = ?")->execute([$today, $uid]);
            }
        }
    } catch (Exception $e) {
        // Tránh gián đoạn game nếu lỗi truy vấn
    }
}
