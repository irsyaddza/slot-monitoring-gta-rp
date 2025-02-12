<?php
require_once 'db_connection.php';

$search_results = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $pdo->prepare("
        SELECT 
            p.firstname,
            p.lastname,
            p.current_tokens,
            p.total_tokens_input,
            h.action_type,
            h.tokens_change,
            h.money_change,
            h.timestamp
        FROM players p
        LEFT JOIN history h ON p.id = h.player_id
        WHERE CONCAT(p.firstname, '_', p.lastname) LIKE ?
        ORDER BY h.timestamp DESC
    ");
    $stmt->execute(["%$search%"]);
    $search_results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slot - Guest Access</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Search Player History</h2>
        
        <form method="GET" action="">
            <div class="form-group">
                <label>Search Player (Firstname_Lastname):</label>
                <input type="text" name="search" required>
            </div>
            <button type="submit">Search</button>
        </form>
        
        <?php if ($search_results): ?>
            <div class="search-results">
                <?php if (count($search_results) > 0): ?>
                    <h3>Results for: <?php echo htmlspecialchars($_GET['search']); ?></h3>
                    
                    <div class="player-summary">
                        <?php 
                        // Hitung total money menggunakan formula yang sama dengan admin.php
                        $money_value = $search_results[0]['current_tokens'] * 5;
                        ?>
                        <h4><?php echo $search_results[0]['firstname'] . ' ' . $search_results[0]['lastname']; ?></h4>
                        <p>Current Tokens: <span class="token-count"><?php echo $search_results[0]['current_tokens']; ?></span></p>
                        <p>Total Tokens Input: <span class="total-input"><?php echo $search_results[0]['total_tokens_input']; ?></span></p>
                        <p>Total Money: $<span class="money-value"><?php echo number_format($money_value, 2); ?></span></p>
                    </div>
                    
                    <h4>History</h4>
                    <table>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Tokens Change</th>
                            <th>Money Change</th>
                        </tr>
                        <?php foreach ($search_results as $result): ?>
                            <?php if ($result['action_type']): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($result['timestamp'])); ?></td>
                                    <td><?php echo $result['action_type']; ?></td>
                                    <td><?php echo $result['tokens_change']; ?></td>
                                    <td>$<?php echo number_format($result['money_change'], 2); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>