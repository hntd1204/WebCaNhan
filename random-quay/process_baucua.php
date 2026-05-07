<?php
session_start();
require 'db.php';
require_once 'app_helpers.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    json_response(['success' => false, 'error' => 'Chưa đăng nhập'], 401);
}

$userId = (int)$_SESSION['user_id'];
$settings = fetch_settings($pdo);
if (!game_enabled($settings, 'baucua')) {
    json_response(['success' => false, 'error' => 'Game Bầu Cua đang tạm khóa bởi admin']);
}
$baucuaMaxDoors = clamp_int($settings['baucua_max_doors'] ?? 3, 1, 5);
$betsRaw = $_POST['bets'] ?? '[]';
$bets = json_decode($betsRaw, true);
if (!is_array($bets)) json_response(['success' => false, 'error' => 'Dữ liệu cược không hợp lệ']);

$validOptions = ['bau', 'cua', 'tom', 'ca', 'ga', 'nai'];
$cleanBets = [];
$totalBet = 0;
foreach ($bets as $key => $amount) {
    if (!in_array($key, $validOptions, true)) json_response(['success' => false, 'error' => 'Cửa cược không hợp lệ']);
    if (!is_numeric($amount)) json_response(['success' => false, 'error' => 'Số tiền cược không hợp lệ']);
    $amount = (int)$amount;
    if ($amount < 0) json_response(['success' => false, 'error' => 'Số tiền cược không hợp lệ']);
    if ($amount > 0) {
        $cleanBets[$key] = $amount;
        $totalBet += $amount;
    }
}

if (empty($cleanBets) || $totalBet <= 0) json_response(['success' => false, 'error' => 'Vui lòng đặt cược!']);
if (count($cleanBets) > $baucuaMaxDoors) json_response(['success' => false, 'error' => 'Bạn chỉ được cược tối đa ' . $baucuaMaxDoors . ' ô!']);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) throw new RuntimeException('User not found');
    if ((int)$user['balance'] < $totalBet) {
        $pdo->rollBack();
        json_response(['success' => false, 'error' => 'Số dư của bạn không đủ!']);
    }

    $betError = validate_bet_amount($totalBet, $settings);
    if ($betError) {
        $pdo->rollBack();
        json_response(['success' => false, 'error' => $betError]);
    }

    $mul = normalize_multiplier($settings['baucua_multiplier'] ?? 1.0, 1.0);

    // Kết quả Bầu Cua random tự nhiên: 3 viên xúc xắc độc lập, không ép thắng/thua.
    $dice = [];
    for ($i = 0; $i < 3; $i++) {
        $dice[] = $validOptions[random_int(0, count($validOptions) - 1)];
    }

    $winnings = 0;
    $winningCounts = [];
    foreach ($cleanBets as $key => $amount) {
        $count = 0;
        foreach ($dice as $d) if ($d === $key) $count++;
        if ($count > 0) {
            $win = (int)round($amount + ($amount * $count * $mul));
            $winnings += $win;
            $winningCounts[$key] = $count;
        }
    }

    $newBalance = (int)$user['balance'] - $totalBet + $winnings;
    $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);
    $netProfit = $winnings - $totalBet;

    $pdo->prepare('INSERT INTO baucua_history (user_id, bet_details, dice_result, total_bet, total_win, net_profit) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$userId, json_encode($cleanBets, JSON_UNESCAPED_UNICODE), implode(',', $dice), $totalBet, $winnings, $netProfit]);

    $missionInfo = complete_mission($pdo, $userId, 'baucua_count');
    $pdo->commit();

    json_response([
        'success' => true,
        'dice' => $dice,
        'winnings' => $winnings,
        'total_bet' => $totalBet,
        'net_profit' => $netProfit,
        'new_balance' => $newBalance,
        'winning_counts' => $winningCounts,
        'mission' => $missionInfo
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['success' => false, 'error' => 'Lỗi hệ thống!']);
}
