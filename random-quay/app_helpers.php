<?php
function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function int_post(string $key, int $default = 0): int
{
    return filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT) !== false && filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT) !== null
        ? (int)filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT)
        : $default;
}

function clamp_int($value, int $min, int $max): int
{
    return max($min, min($max, (int)$value));
}

function normalize_multiplier($value, float $default): float
{
    $value = is_numeric($value) ? (float)$value : $default;
    if ($value <= 0) return $default;
    return min($value, 100.0);
}

function fetch_settings(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    return is_array($settings) ? $settings : [];
}

function money_round($amount): int
{
    return max(0, (int)round(((float)$amount) / 1000) * 1000);
}

function draw_card_by_rule(array &$deck, callable $predicate): ?array
{
    foreach ($deck as $idx => $card) {
        if ($predicate($card)) {
            $picked = $card;
            unset($deck[$idx]);
            $deck = array_values($deck);
            return $picked;
        }
    }
    return null;
}

function complete_mission(PDO $pdo, int $userId, string $missionKey): array
{
    if (!preg_match('/^[a-z0-9_]+$/', $missionKey)) {
        return ['rewarded' => false];
    }

    try {
        $pdo->prepare("UPDATE users SET `$missionKey` = COALESCE(`$missionKey`, 0) + 1 WHERE id = ?")->execute([$userId]);
    } catch (Exception $e) {
        return ['rewarded' => false];
    }

    $stmt = $pdo->prepare("SELECT `$missionKey` FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $currentCount = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT target_count, reward_spins FROM mission_settings WHERE mission_key = ? ORDER BY target_count ASC");
    $stmt->execute([$missionKey]);
    $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($missions)) {
        return ['rewarded' => false];
    }

    $missionInfo = ['rewarded' => false, 'current' => $currentCount];
    foreach ($missions as $m) {
        $target = (int)$m['target_count'];
        if ($currentCount === $target) {
            $reward = max(0, (int)$m['reward_spins']);
            if ($reward > 0) {
                $pdo->prepare("UPDATE users SET spins_available = spins_available + ? WHERE id = ?")->execute([$reward, $userId]);
            }
            return ['rewarded' => true, 'current' => $currentCount, 'target' => $target, 'reward_spins' => $reward];
        }
        if (!isset($missionInfo['target']) && $currentCount < $target) {
            $missionInfo['target'] = $target;
        }
    }

    if (!isset($missionInfo['target']) && !empty($missions)) {
        $missionInfo['target'] = (int)end($missions)['target_count'];
    }
    return $missionInfo;
}

function bool_setting($value, bool $default = true): bool
{
    if ($value === null || $value === '') return $default;
    return (int)$value === 1;
}

function clamp_money($value, int $default = 0): int
{
    if (!is_numeric($value)) return max(0, $default);
    return max(0, (int)$value);
}

function validate_bet_amount(int $bet, array $settings): ?string
{
    $minBet = clamp_money($settings['minigame_min_bet'] ?? 1000, 1000);
    $maxBet = clamp_money($settings['minigame_max_bet'] ?? 1000000, 1000000);
    if ($maxBet > 0 && $maxBet < $minBet) { [$minBet, $maxBet] = [$maxBet, $minBet]; }
    if ($bet < $minBet) return 'Tiền cược tối thiểu là ' . number_format($minBet) . 'đ';
    if ($maxBet > 0 && $bet > $maxBet) return 'Tiền cược tối đa là ' . number_format($maxBet) . 'đ';
    return null;
}

function game_enabled(array $settings, string $key): bool
{
    return bool_setting($settings[$key . '_enabled'] ?? 1, true);
}

function should_user_win(int $winRate): bool
{
    return random_int(1, 100) <= clamp_int($winRate, 0, 100);
}

function pull_matching_item(array &$items, callable $predicate)
{
    foreach ($items as $idx => $item) {
        if ($predicate($item)) {
            $picked = $item;
            unset($items[$idx]);
            $items = array_values($items);
            return $picked;
        }
    }
    return null;
}

function safe_json_decode_array($value): array
{
    $decoded = is_string($value) ? json_decode($value, true) : $value;
    return is_array($decoded) ? $decoded : [];
}



function public_game_config(array $settings, string $gameKey): array
{
    $minBet = clamp_money($settings['minigame_min_bet'] ?? 1000, 1000);
    $maxBet = clamp_money($settings['minigame_max_bet'] ?? 1000000, 1000000);
    if ($maxBet > 0 && $maxBet < $minBet) { [$minBet, $maxBet] = [$maxBet, $minBet]; }
    return [
        'enabled' => game_enabled($settings, $gameKey),
        'min_bet' => $minBet,
        'max_bet' => $maxBet,
        'multiplier' => normalize_multiplier($settings[$gameKey . '_multiplier'] ?? 1, 1),
        'baucua_max_doors' => clamp_int($settings['baucua_max_doors'] ?? 3, 1, 5),
        'mines_bombs' => clamp_int($settings['mines_bombs'] ?? 3, 1, 24),
        'mines_cashout_min_steps' => clamp_int($settings['mines_cashout_min_steps'] ?? 1, 0, 24),
    ];
}

function bet_chip_options(int $minBet, int $maxBet): array
{
    $base = [1000, 5000, 10000, 20000, 50000, 100000, 200000, 500000, 1000000];
    $chips = [];
    foreach ($base as $chip) {
        if ($chip >= $minBet && ($maxBet <= 0 || $chip <= $maxBet)) $chips[] = $chip;
    }
    if (empty($chips)) $chips[] = $minBet;
    return array_values(array_unique($chips));
}
