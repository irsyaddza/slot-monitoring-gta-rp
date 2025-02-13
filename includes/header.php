<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Slot - Dealer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="../assets/js/algorithm.js"></script>
    <style>
        .header {
            background: linear-gradient(135deg, #343131 0%, #343131 100%);
            padding: 1rem;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo {
            width: 50px;
            height: 50px;
        }
        
        .brand-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #FFB22C;
        }
        
        .header-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
        }
        
        .search-form input {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .search-form button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background: gold;
            color: #343131;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .search-form button:hover {
            background: #FFB22C;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FFB22C;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="../assets/images/logo.png" alt="Casino Logo" class="logo">
                <span class="brand-name">Hillside Ridge Casino</span>
            </div>
            
            <div class="header-nav">
                <form class="search-form" action="guest.php" method="GET">
                    <input type="text" name="search" placeholder="Search players...">
                    
                </form>
                
                <div class="admin-info">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../actions/logout.php" class="btn-secondary">Logout</a>
                </div>
            </div>
        </div>
    </header>