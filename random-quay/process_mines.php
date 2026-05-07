<?php
session_start();
require 'db.php';
require_once 'app_helpers.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    json_response(['success' => false, 'error' => 'Chưa đăng nhập'], 401);
}

$userId = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$settings = fetch_settings($pdo);
if (!game_enabled($settings, 'mines') && $action === 'start') {
    json_response(['success' => false, 'error' => 'Game Dò Mìn đang tạm khóa bởi admin']);
}

if ($action === 'start') {
    if (isset($_SESSION['mines']) && ($_SESSION['mines']['status'] ?? '') === 'playing') {
        json_response(['success' => false, 'error' => 'Bạn đang có ván Dò mìn chưa kết thúc. Hãy mở ô hoặc chốt lời trước.']);
    }

    $bet = int_post('bet');
    if ($bet <= 0) json_response(['success' => false, 'error' => 'Cược không hợp lệ']);
    $betError = validate_bet_amount($bet, $settings);
    if ($betError) json_response(['success' => false, 'error' => $betError]);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) throw new RuntimeException('User not found');
        if ((int)$user['balance'] < $bet) {
            $pdo->rollBack();
            json_response(['success' => false, 'error' => 'Không đủ số dư']);
        }

        $bombs = clamp_int($settings['mines_bombs'] ?? 3, 1, 24);
        $minesMul = normalize_multiplier($settings['mines_multiplier'] ?? 1.2, 1.2);

        $newBalance = (int)$user['balance'] - $bet;
        $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);

        $board = array_fill(0, 25, 'safe');
        $bombIndexes = (array)array_rand($board, $bombs);
        foreach ($bombIndexes as $i) $board[(int)$i] = 'bomb';

        $_SESSION['mines'] = [
            'bet' => $bet,
            'pot' => $bet,
            'board' => $board,
            'opened' => [],
            'bombs' => $bombs,
            'mines_mul' => $minesMul,
            'step' => 0,
            'status' => 'playing'
        ];

        $pdo->commit();
        json_response(['success' => true, 'balance' => $newBalance, 'pot' => $bet, 'bombs' => $bombs]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        unset($_SESSION['mines']);
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

if ($action === 'open') {
    if (!isset($_SESSION['mines']) || ($_SESSION['mines']['status'] ?? '') !== 'playing') {
        json_response(['success' => false, 'error' => 'Ván chơi không hợp lệ']);
    }

    $index = int_post('index', -1);
    if ($index < 0 || $index > 24) json_response(['success' => false, 'error' => 'Ô mở không hợp lệ']);

    $mines = $_SESSION['mines'];
    $mines['opened'] = $mines['opened'] ?? [];
    if (in_array($index, $mines['opened'], true)) {
        json_response(['success' => false, 'error' => 'Ô này đã mở rồi']);
    }

    // Board đã được tạo ngẫu nhiên lúc bắt đầu ván; mở ô nào kiểm tra đúng ô đó, không dời mìn.

    if (($mines['board'][$index] ?? 'safe') === 'bomb') {
        $pdo->beginTransaction();
        try {
            $missionInfo = complete_mission($pdo, $userId, 'mines_count');
            $pdo->prepare('INSERT INTO mines_history (user_id, bet, win, net_profit, bombs, steps) VALUES (?, ?, 0, ?, ?, ?)')
                ->execute([$userId, (int)$mines['bet'], -(int)$mines['bet'], (int)$mines['bombs'], (int)$mines['step']]);
            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $missionInfo = ['rewarded' => false];
        }
        unset($_SESSION['mines']);
        json_response(['success' => true, 'is_bomb' => true, 'board' => $mines['board'], 'mission' => $missionInfo]);
    }

    $mines['opened'][] = $index;
    $mines['step'] = (int)$mines['step'] + 1;
    $mines['pot'] = money_round((int)$mines['pot'] * (float)$mines['mines_mul']);
    $safeTotal = 25 - (int)$mines['bombs'];
    $_SESSION['mines'] = $mines;

    json_response(['success' => true, 'is_bomb' => false, 'pot' => (int)$mines['pot'], 'step' => (int)$mines['step'], 'opened' => $mines['opened'], 'can_cashout' => (int)$mines['step'] >= clamp_int($settings['mines_cashout_min_steps'] ?? 1, 0, 24), 'all_safe_opened' => (int)$mines['step'] >= $safeTotal]);
}

if ($action === 'cashout') {
    if (!isset($_SESSION['mines']) || ($_SESSION['mines']['status'] ?? '') !== 'playing') {
        json_response(['success' => false, 'error' => 'Ván chơi không tồn tại']);
    }
    $mines = $_SESSION['mines'];
    $minCashoutSteps = clamp_int($settings['mines_cashout_min_steps'] ?? 1, 0, 24);
    if ((int)($mines['step'] ?? 0) < $minCashoutSteps) {
        json_response(['success' => false, 'error' => 'Bạn cần mở tối thiểu ' . $minCashoutSteps . ' ô an toàn mới được chốt lời']);
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) throw new RuntimeException('User not found');

        $winnings = (int)$mines['pot'];
        $newBalance = (int)$user['balance'] + $winnings;
        $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);
        $pdo->prepare('INSERT INTO mines_history (user_id, bet, win, net_profit, bombs, steps) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$userId, (int)$mines['bet'], $winnings, $winnings - (int)$mines['bet'], (int)$mines['bombs'], (int)$mines['step']]);
        $missionInfo = ((int)$mines['step'] > 0) ? complete_mission($pdo, $userId, 'mines_count') : ['rewarded' => false];
        $pdo->commit();
        unset($_SESSION['mines']);

        json_response(['success' => true, 'winnings' => $winnings, 'balance' => $newBalance, 'mission' => $missionInfo]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

json_response(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
