<?php
require_once '../config/db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['player_id'])) {
    echo json_encode(['success' => false, 'error' => 'Player ID required']);
    exit;
}

$player_id = $_GET['player_id'];

$stmt = $pdo->prepare("SELECT current_tokens FROM players WHERE id = ?");
$stmt->execute([$player_id]);
$player = $stmt->fetch();

if ($player && $player['current_tokens'] >= 5) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Not enough tokens']);
}
?>