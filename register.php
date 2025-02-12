<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $secret_code = $_POST['secret_code'];
    
    $role = ($secret_code === 'bandarhillside') ? 'admin' : 'user';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, secret_code, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $secret_code, $role]);
        header("Location: login.php");
        exit();
    } catch(PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slot - Register</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Slot Machine Monitoring</h2>
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Secret Code:</label>
                <input type="text" name="secret_code">
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <div class="links">
            <a href="login.php">Login</a>
            <a href="guest.php">Guest Access</a>
        </div>
    </div>
</body>
</html>