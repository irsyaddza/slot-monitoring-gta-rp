<?php
require_once __DIR__ . '/config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];  
        
        if ($user['role'] === 'admin') {
            header("Location: pages/admin.php");  
        } else {
            header("Location: pages/player.php"); 
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slot - Login</title>
    <link rel="stylesheet" href="../slot/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Slot Machine Monitoring</h2>
        <h2>Login</h2>
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
            
            <button type="submit">Login</button>
        </form>
        
        <div class="links">
            <a href="../slot/pages/register.php">Register</a>
            <a href="../slot/pages/guest.php">Guest Access</a>
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