<?php
require_once '../config/db_connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Check if it's a token addition or game action
$isTokenAddition = isset($data['tokens']);

if ($isTokenAddition) {
    if (!isset($data['player_id']) || !isset($data['tokens'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required data']);
        exit;
    }
    $player_id = $data['player_id'];
    $tokens = $data['tokens'];
    $reward = $tokens;
    $cost = 0;
    $action_type = 'token_add';
} else {
    if (!isset($data['player_id']) || !isset($data['reward']) || !isset($data['cost'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required data']);
        exit;
    }
    $player_id = $data['player_id'];
    $reward = $data['reward'];
    $cost = $data['cost'];
    $action_type = $data['action_type'] ?? 'game_action';
}

try {
    $pdo->beginTransaction();

    // Get current token value and total_tokens_input
    $stmt = $pdo->prepare("SELECT current_tokens, total_tokens_input FROM players WHERE id = ?");
    $stmt->execute([$player_id]);
    $player = $stmt->fetch();

    if (!$player) {
        throw new Exception('Player not found');
    }

    $current_tokens = $player['current_tokens'];
    $total_tokens_input = $player['total_tokens_input'];

    // Check if player has enough tokens for the cost
    if ($cost > 0 && $current_tokens < $cost) {
        throw new Exception('Not enough tokens');
    }

    // Calculate new balance
    $new_token_balance = $current_tokens + $reward - $cost;

    // Update new total_tokens_input only for token addition
    $new_total_tokens_input = $total_tokens_input;
    if ($isTokenAddition) {
        $new_total_tokens_input += $tokens;
    }

    // Update player's tokens
    $stmt = $pdo->prepare("
UPDATE players 
SET 
    current_tokens = ?,
    total_tokens_input = ?,
    total_money = ? * 5  -- Tambahkan update total_money
WHERE id = ?
");
    $stmt->execute([
        $new_token_balance,
        $new_total_tokens_input,
        $new_token_balance,  // total_money = current_tokens * 5
        $player_id
    ]);

    // Record in history
    $tokens_change = $reward - $cost;
    $money_change = $tokens_change * 5;
    $stmt = $pdo->prepare("
        INSERT INTO history 
        (player_id, action_type, tokens_change, money_change) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$player_id, $action_type, $tokens_change, $money_change]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'new_balance' => $new_token_balance,
        'total_tokens_input' => $new_total_tokens_input
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
