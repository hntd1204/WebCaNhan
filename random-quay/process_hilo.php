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
if (!game_enabled($settings, 'hilo') && $action === 'start') {
    json_response(['success' => false, 'error' => 'Game Hi-Lo đang tạm khóa bởi admin']);
}
$hiloMul = normalize_multiplier($settings['hilo_multiplier'] ?? 1.2, 1.2);

function getDeckHilo(): array
{
    $suits = ['♠', '♣', '♦', '♥'];
    $ranks = ['2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, 'J' => 11, 'Q' => 12, 'K' => 13, 'A' => 14];
    $deck = [];
    foreach ($suits as $suit) foreach ($ranks as $label => $val) $deck[] = ['rank' => $label, 'val' => $val, 'suit' => $suit, 'color' => in_array($suit, ['♦', '♥'], true) ? 'red' : 'black'];
    shuffle($deck);
    return $deck;
}

function saveHiloHistory(PDO $pdo, int $userId, int $bet, int $win, int $streak): void
{
    $pdo->prepare('INSERT INTO hilo_history (user_id, bet, win, net_profit, streak) VALUES (?, ?, ?, ?, ?)')->execute([$userId, $bet, $win, $win - $bet, $streak]);
}

if ($action === 'start') {
    if (isset($_SESSION['hilo']) && ($_SESSION['hilo']['status'] ?? '') === 'playing') {
        json_response(['success' => false, 'error' => 'Bạn đang có ván Hi-Lo chưa kết thúc. Hãy đoán hoặc chốt lời trước.']);
    }
    $bet = int_post('bet');
    if ($bet <= 0) json_response(['success' => false, 'error' => 'Tiền cược không hợp lệ']);
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
            json_response(['success' => false, 'error' => 'Số dư không đủ!']);
        }
        $newBalance = (int)$user['balance'] - $bet;
        $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);
        $pdo->commit();

        $deck = getDeckHilo();
        $firstCard = array_pop($deck);
        $_SESSION['hilo'] = ['status' => 'playing', 'deck' => $deck, 'current_card' => $firstCard, 'bet' => $bet, 'pot' => $bet, 'streak' => 0];
        json_response(['success' => true, 'card' => $firstCard, 'pot' => $bet, 'balance' => $newBalance]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        unset($_SESSION['hilo']);
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

if ($action === 'guess') {
    if (!isset($_SESSION['hilo']) || ($_SESSION['hilo']['status'] ?? '') !== 'playing') json_response(['success' => false, 'error' => 'Ván chơi không tồn tại']);
    $choice = $_POST['choice'] ?? '';
    if (!in_array($choice, ['hi', 'lo'], true)) json_response(['success' => false, 'error' => 'Lựa chọn không hợp lệ']);

    $hilo = $_SESSION['hilo'];
    $currentCard = $hilo['current_card'];
    if (empty($hilo['deck'])) $hilo['deck'] = getDeckHilo();

    // Rút lá ngẫu nhiên từ bộ bài đã xáo, không can thiệp theo lựa chọn của người chơi.
    $nextCard = array_pop($hilo['deck']);

    $isWin = ($nextCard['val'] > $currentCard['val'] && $choice === 'hi') || ($nextCard['val'] < $currentCard['val'] && $choice === 'lo');
    $isTie = $nextCard['val'] == $currentCard['val'];
    $hilo['current_card'] = $nextCard;

    if ($isWin) {
        $hilo['streak'] = (int)$hilo['streak'] + 1;
        $hilo['pot'] = money_round((int)$hilo['pot'] * $hiloMul);
        $_SESSION['hilo'] = $hilo;
        json_response(['success' => true, 'is_end' => false, 'card' => $nextCard, 'pot' => (int)$hilo['pot'], 'message' => 'Chính xác! 🎉 (Pot: ' . number_format((int)$hilo['pot']) . 'đ)']);
    }

    if ($isTie) {
        $_SESSION['hilo'] = $hilo;
        json_response(['success' => true, 'is_end' => false, 'card' => $nextCard, 'pot' => (int)$hilo['pot'], 'message' => 'Hòa! Rút tiếp lá nữa 🤝']);
    }

    $pdo->beginTransaction();
    try {
        saveHiloHistory($pdo, $userId, (int)$hilo['bet'], 0, (int)$hilo['streak']);
        $missionInfo = complete_mission($pdo, $userId, 'hilo_count');
        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $missionInfo = ['rewarded' => false];
    }
    unset($_SESSION['hilo']);
    json_response(['success' => true, 'is_end' => true, 'card' => $nextCard, 'pot' => 0, 'message' => 'Sai rồi! Bạn mất trắng 💥', 'mission' => $missionInfo]);
}

if ($action === 'cashout') {
    if (!isset($_SESSION['hilo']) || ($_SESSION['hilo']['status'] ?? '') !== 'playing') json_response(['success' => false, 'error' => 'Ván chơi không tồn tại']);
    $hilo = $_SESSION['hilo'];
    $winnings = (int)$hilo['pot'];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) throw new RuntimeException('User not found');
        $newBalance = (int)$user['balance'] + $winnings;
        $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);
        saveHiloHistory($pdo, $userId, (int)$hilo['bet'], $winnings, (int)$hilo['streak']);
        $missionInfo = ((int)$hilo['streak'] > 0) ? complete_mission($pdo, $userId, 'hilo_count') : ['rewarded' => false];
        $pdo->commit();
        unset($_SESSION['hilo']);
        json_response(['success' => true, 'balance' => $newBalance, 'winnings' => $winnings, 'mission' => $missionInfo]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

json_response(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
