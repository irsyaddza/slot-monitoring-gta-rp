<?php
require_once 'db_connection.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Add player (only through the form)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_player'])) {
    $name_parts = explode('_', $_POST['player_name']);
    if (count($name_parts) === 2) {
        $firstname = $name_parts[0];
        $lastname = $name_parts[1];

        // Check if player already exists
        $stmt = $pdo->prepare("SELECT id FROM players WHERE firstname = ? AND lastname = ?");
        $stmt->execute([$firstname, $lastname]);
        if ($stmt->fetch()) {
            $error = "Player already exists";
        } else {
            $stmt = $pdo->prepare("INSERT INTO players (firstname, lastname, added_by, current_tokens, total_tokens_input, total_money) VALUES (?, ?, ?, 0, 0, 0)");
            $stmt->execute([$firstname, $lastname, $_SESSION['user_id']]);
            $success = "Player added successfully";
        }
    } else {
        $error = "Invalid name format. Use Firstname_Lastname format";
    }
}

// Delete player
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_player'])) {
    $name_parts = explode('_', $_POST['delete_player_name']);
    if (count($name_parts) === 2) {
        $firstname = $name_parts[0];
        $lastname = $name_parts[1];

        try {
            $pdo->beginTransaction();

            // Find the player
            $stmt = $pdo->prepare("SELECT id FROM players WHERE firstname = ? AND lastname = ?");
            $stmt->execute([$firstname, $lastname]);
            $player = $stmt->fetch();

            if ($player) {
                // Delete history first (due to foreign key constraint)
                $stmt = $pdo->prepare("DELETE FROM history WHERE player_id = ?");
                $stmt->execute([$player['id']]);

                // Delete player
                $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
                $stmt->execute([$player['id']]);

                $pdo->commit();
                $success = "Player and history deleted successfully";
            } else {
                $error = "Player not found";
                $pdo->rollBack();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error deleting player: " . $e->getMessage();
        }
    } else {
        $error = "Invalid name format. Use Firstname_Lastname format";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Slot - Dealer Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Dealer Dashboard</h2>

        <div class="nav-links">
            <a href="guest.php" class="btn-primary">🔍 Search Players History</a>
        </div>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

        <div class="admin-actions">
            <!-- Add Player Form -->
            <div class="action-box">
                <h3>Add New Player</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Player Name (Firstname_Lastname):</label>
                        <input type="text" name="player_name" required>
                    </div>
                    <button type="submit" name="add_player" class="btn-primary">Add Player</button>
                </form>
            </div>

            <!-- Delete Player Form -->
            <div class="action-box">
                <h3>Delete Player</h3>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this player and all their history? This action cannot be undone.');">
                    <div class="form-group">
                        <label>Player Name (Firstname_Lastname):</label>
                        <input type="text" name="delete_player_name" required>
                    </div>
                    <button type="submit" name="delete_player" class="btn-danger">Delete Player</button>
                </form>
            </div>
        </div>

        <div class="manage-players">
            <h3>Manage Players</h3>
            <?php
            $stmt = $pdo->query("SELECT * FROM players ORDER BY id ASC");
            $players = $stmt->fetchAll();

            foreach ($players as $player) {
                $money_value = $player['current_tokens'] * 5;
                echo "<div class='player-card' id='player-{$player['id']}'>";
                echo "<h4>{$player['firstname']} {$player['lastname']}</h4>";
                echo "<p>Current Tokens: <span class='token-count'>{$player['current_tokens']}</span></p>";
                echo "<p>Total Money: $<span class='money-value'>" . number_format($money_value, 2) . "</span></p>";
                echo "<p>Total Tokens Input: <span class='total-input'>{$player['total_tokens_input']}</span></p>";
                echo "<form onsubmit='return addTokens(event, {$player['id']})'>";
                echo "<input type='number' name='tokens' placeholder='Add tokens' required min='1'>";
                echo "<button type='submit'>Add Tokens</button>";
                echo "</form>";
                echo "<button onclick='startGame({$player['id']})' class='game-button'>Start Game (-5 tokens)</button>";
                echo "</div>";
            }
            ?>
        </div>
        <div class="nav-links">
            <a href="logout.php" class="btn-secondary">Logout</a>
        </div>
    </div>
    <script src="algorithm.js"></script>
</body>

</html>