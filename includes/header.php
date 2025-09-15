<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <h2><i class="fas fa-leaf"></i> Ceylon Fresh</h2>
        </div>
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="product.php" class="nav-link">Products</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="cart.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin'): ?>
                        <li><a href="admin/dashboad.php" class="nav-link admin-btn"><i class="fas fa-cog"></i> Admin</a></li>
                    <?php endif; ?>
                    <li><a href="?logout=1" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                    <li><a href="register.php" class="nav-link btn-register">Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
    </div>
</header>