<?php
// Enhanced session configuration 
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);  
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();
include('database/connection.php');
include('includes/authentication.php');

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    clear_user_session();
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Check if user is already logged in
if (is_logged_in()) {
    redirect_after_login();
}

if (isset($_POST['login-btn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Enhanced debug loggin 
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $browser = 'Unknown';
    if (strpos($user_agent, 'Chrome') !== false && strpos($user_agent, 'Edg') === false) {
        $browser = 'Chrome';
    } elseif (strpos($user_agent, 'Brave') !== false) {
        $browser = 'Brave';
    }
    
    error_log("=== LOGIN ATTEMPT ($browser) ===");
    error_log("Email: $email");
    error_log("Password length: " . strlen($password));
    error_log("Session ID: " . session_id());
    error_log("POST data: " . print_r($_POST, true));

    if (!empty($email) && !empty($password)) {
        $user = authenticate_user($email, $password, $conn);
        
        if ($user) {
            // Set user session
            set_user_session($user);
            
            // Debug logging
            error_log("Login successful for user: " . $user['email']);
            
            // Redirect based on user level
            if ($user['user_level'] === 'admin') {
                header("Location: admin/dashboad.php");
            } else {
                redirect_after_login();
            }
            exit();
        } else {
            $error = "Invalid email or password.";
            error_log("Login failed for email: " . $email);
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body.login-page { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("assets/images/login.jpg") no-repeat fixed center / cover; margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .login-main-content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .site-header, .site-footer { position: relative !important; z-index: 1000; }
        .login-container { background: linear-gradient(rgba(5,5,5,0.6), rgba(0,0,0,0.8)); padding: 60px 40px; border-radius: 15px; width: 100%; max-width: 400px; box-shadow: 0 5px 25px rgba(0,0,0,0.3); }
        .login-container h2 { margin-bottom: 25px; color: #f5f4f4; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #fcfcfc; font-weight: 500; }
        .form-group input[type="email"], .form-group input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #e9e2e2; border-radius: 6px; font-size: 15px; }
        .btn { width: 100%; background-color: #165207; color: #fff; border: none; padding: 12px; border-radius: 8px; font-size: 16px; cursor: pointer; }
        .btn:hover { background-color: #177517; }
        .error { background-color: #ffe6e6; color: #cc0000; border: 1px solid #ffcccc; padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; text-align: center; }
        .signup-link { text-align: center; margin-top: 15px; font-size: 14px; color: white; }
        .signup-link a { color: #e4e735; text-decoration: none; }
        .signup-link a:hover { text-decoration: underline; }
        @media screen and (max-width:480px) { .login-container { padding: 30px 20px; } }
    </style>
</head>
<body class="login-page">
    <?php include_once 'includes/header.php'; ?>
    
    <div class="login-main-content">
        <div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="post" action="login.php" autocomplete="on" novalidate>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required autocomplete="username" spellcheck="false">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <input type="submit" name="login-btn" value="Login" class="btn">
    </form>

    <p class="signup-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>
         
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                   
                    if (this.value && this.value.includes('@ceylonfresh.local')) {
                        this.value = this.value.replace('@ceylonfresh.local', '@ceylonfresh.com');
                    }
                });
            }
        });
    </script>
</body>
</html>