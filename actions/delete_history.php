<?php
require_once '../config/db_connection.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['player_id'])) {
    echo json_encode(['success' => false, 'error' => 'Player ID required']);
    exit;
}

$player_id = $data['player_id'];

try {
    $pdo->beginTransaction();
    
    // Delete all history entries for this player
    $stmt = $pdo->prepare("DELETE FROM history WHERE player_id = ?");
    $stmt->execute([$player_id]);
    
    // Reset player's statistics except for current_tokens
    $stmt = $pdo->prepare("
        UPDATE players 
        SET 
            total_tokens_input = 0,
            tokens_spent = 0,
            total_money = 0
        WHERE id = ?
    ");
    $stmt->execute([$player_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>