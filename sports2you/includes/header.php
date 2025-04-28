<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Sports2You'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo-link">
                    <img src="images/logo.png" alt="Sports2You Logo" class="logo">
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <ul class="nav-links" id="navLinks">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                        <li><a href="browse.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'browse.php' ? 'active' : ''; ?>">Browse Games</a></li>
                        <li><a href="availability.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'availability.php' ? 'active' : ''; ?>">My Availability</a></li>
                        <li><a href="create_game.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'create_game.php' ? 'active' : ''; ?>">Create Game</a></li>
                        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
                        <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                        <li><a href="signup.php" class="btn btn-primary">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
