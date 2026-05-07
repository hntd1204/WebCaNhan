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
if (!game_enabled($settings, 'blackjack') && $action === 'deal') {
    json_response(['success' => false, 'error' => 'Game Xì Dách đang tạm khóa bởi admin']);
}
$bjMul = normalize_multiplier($settings['blackjack_multiplier'] ?? 2.0, 2.0);

function getDeck(): array
{
    $suits = ['♠', '♣', '♦', '♥'];
    $ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
    $deck = [];
    foreach ($suits as $suit) foreach ($ranks as $rank) $deck[] = ['rank' => $rank, 'suit' => $suit, 'color' => in_array($suit, ['♦', '♥'], true) ? 'red' : 'black'];
    shuffle($deck);
    return $deck;
}

function calcScore(array $hand): int
{
    $score = 0; $aces = 0;
    foreach ($hand as $card) {
        if ($card['rank'] === 'A') { $aces++; $score += 11; }
        elseif (in_array($card['rank'], ['J', 'Q', 'K'], true)) $score += 10;
        else $score += (int)$card['rank'];
    }
    while ($score > 21 && $aces > 0) { $score -= 10; $aces--; }
    return $score;
}

function checkType(array $hand): string
{
    if (count($hand) === 2) {
        $aces = 0; $tens = 0;
        foreach ($hand as $c) {
            if ($c['rank'] === 'A') $aces++;
            if (in_array($c['rank'], ['10', 'J', 'Q', 'K'], true)) $tens++;
        }
        if ($aces === 2) return 'xibang';
        if ($aces === 1 && $tens === 1) return 'xidach';
    }
    if (count($hand) === 5 && calcScore($hand) <= 21) return 'ngulinh';
    if (calcScore($hand) > 21) return 'quac';
    return 'thuong';
}

function saveBlackjackHistory(PDO $pdo, int $userId, int $bet, int $win): void
{
    $pdo->prepare('INSERT INTO blackjack_history (user_id, bet, win, net_profit) VALUES (?, ?, ?, ?)')->execute([$userId, $bet, $win, $win - $bet]);
}

function finishBlackjack(PDO $pdo, int $userId, array $bj, int $winnings, string $message, array $dealerHand): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) throw new RuntimeException('User not found');
        $newBalance = (int)$user['balance'] + $winnings;
        if ($winnings > 0) $pdo->prepare('UPDATE users SET balance = ? WHERE id = ?')->execute([$newBalance, $userId]);
        saveBlackjackHistory($pdo, $userId, (int)$bj['bet'], $winnings);
        $missionInfo = complete_mission($pdo, $userId, 'blackjack_count');
        $pdo->commit();
        unset($_SESSION['bj']);
        json_response([
            'success' => true,
            'is_end' => true,
            'player' => $bj['player'],
            'dealer' => $dealerHand,
            'player_score' => calcScore($bj['player']),
            'dealer_score' => calcScore($dealerHand),
            'winnings' => $winnings,
            'net_profit' => $winnings - (int)$bj['bet'],
            'balance' => $newBalance,
            'message' => $message,
            'mission' => $missionInfo
        ]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

if ($action === 'deal') {
    if (isset($_SESSION['bj']) && ($_SESSION['bj']['status'] ?? '') === 'playing') {
        json_response(['success' => false, 'error' => 'Bạn đang có ván Xì Dách chưa kết thúc. Hãy rút bài hoặc dằn trước.']);
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

        // Chia bài ngẫu nhiên tự nhiên từ bộ bài đã xáo, không ép bài thắng/thua.
        $deck = getDeck();
        $playerHand = [array_pop($deck), array_pop($deck)];
        $dealerHand = [array_pop($deck), array_pop($deck)];

        $bj = ['deck' => $deck, 'player' => $playerHand, 'dealer' => $dealerHand, 'bet' => $bet, 'status' => 'playing'];
        $_SESSION['bj'] = $bj;

        $pType = checkType($playerHand);
        if (in_array($pType, ['xibang', 'xidach'], true)) {
            $dType = checkType($dealerHand);
            $winnings = in_array($dType, ['xibang', 'xidach'], true) ? $bet : (int)round($bet * $bjMul);
            $message = $winnings === $bet ? 'Hòa! Cả hai đều có Xì dách/Xì bàng.' : '🎉 THẮNG! Bạn có ' . ($pType === 'xibang' ? 'Xì Bàng' : 'Xì Dách');
            finishBlackjack($pdo, $userId, $bj, $winnings, $message, $dealerHand);
        }

        json_response(['success' => true, 'is_end' => false, 'player' => $playerHand, 'dealer' => [$dealerHand[0]], 'player_score' => calcScore($playerHand), 'balance' => $newBalance]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        unset($_SESSION['bj']);
        json_response(['success' => false, 'error' => 'Lỗi hệ thống']);
    }
}

if ($action === 'hit') {
    if (!isset($_SESSION['bj']) || ($_SESSION['bj']['status'] ?? '') !== 'playing') json_response(['success' => false, 'error' => 'Ván chơi không tồn tại']);
    $bj = $_SESSION['bj'];
    if (empty($bj['deck'])) $bj['deck'] = getDeck();

    $nextCard = array_pop($bj['deck']);
    $bj['player'][] = $nextCard;

    $score = calcScore($bj['player']);
    if ($score > 21) {
        finishBlackjack($pdo, $userId, $bj, 0, '💥 QUẮC! Bạn vượt quá 21 điểm.', $bj['dealer']);
    }
    if (count($bj['player']) === 5) {
        finishBlackjack($pdo, $userId, $bj, (int)round((int)$bj['bet'] * $bjMul), '🌟 NGŨ LINH! Bạn thắng tuyệt đối.', $bj['dealer']);
    }

    $_SESSION['bj'] = $bj;
    json_response(['success' => true, 'is_end' => false, 'player' => $bj['player'], 'player_score' => $score]);
}

if ($action === 'stand') {
    if (!isset($_SESSION['bj']) || ($_SESSION['bj']['status'] ?? '') !== 'playing') json_response(['success' => false, 'error' => 'Ván chơi không tồn tại']);
    $bj = $_SESSION['bj'];
    $playerScore = calcScore($bj['player']);
    if ($playerScore > 21) finishBlackjack($pdo, $userId, $bj, 0, '💥 QUẮC! Bạn vượt quá 21 điểm.', $bj['dealer']);

    // Nhà cái rút bài theo luật cố định, bài lấy ngẫu nhiên từ bộ bài; không chọn bài có lợi cho bên nào.
    while (count($bj['dealer']) < 5 && !empty($bj['deck'])) {
        $dealerScore = calcScore($bj['dealer']);
        if ($dealerScore >= 16) break;
        $bj['dealer'][] = array_pop($bj['deck']);
    }

    $dealerScore = calcScore($bj['dealer']);
    $dealerType = checkType($bj['dealer']);
    $winnings = 0;
    if ($dealerType === 'quac') { $winnings = (int)round((int)$bj['bet'] * $bjMul); $message = 'Nhà cái Quắc! BẠN THẮNG 🎉'; }
    elseif ($dealerType === 'ngulinh') { $message = 'Nhà cái Ngũ Linh! BẠN THUA 💥'; }
    elseif ($playerScore > $dealerScore) { $winnings = (int)round((int)$bj['bet'] * $bjMul); $message = 'BẠN THẮNG 🎉 (' . $playerScore . ' vs ' . $dealerScore . ')'; }
    elseif ($playerScore < $dealerScore) { $message = 'BẠN THUA 💥 (' . $playerScore . ' vs ' . $dealerScore . ')'; }
    else { $winnings = (int)$bj['bet']; $message = 'HÒA VỐN 🤝 (' . $playerScore . ' điểm)'; }

    finishBlackjack($pdo, $userId, $bj, $winnings, $message, $bj['dealer']);
}

json_response(['success' => false, 'error' => 'Yêu cầu không hợp lệ']);
