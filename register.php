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
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Us</h4>
                <p>Slot Machine Monitoring System - Hillside Ridge Casino.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="https://jogjagamers.org/topic/238473-guide-a-fairly-gambling-method/#comment-2311683">Guide Slot</a></li>
                    <li><a href="https://bit.ly/dcnegra">Discord</a></li>
                    <li><a href="https://jogjagamers.org/topic/323994-la-sombra-negra-de-cartel-chapter-i-the-manufacturers-mojo/">Our Thread</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Info</h4>
                <p>Email: support@hillsideridge.com</p>
                <p>Email: support@cieloglam.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Cielo Glam Enterprises. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>