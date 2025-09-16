<?php
session_start();
include('database/connection.php');
include('includes/authentication.php');

$error = "";
$success = "";

// Check if user is already logged in
if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['register-btn'])) {
    $user_data = [
        'username' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'full_name' => trim($_POST['name']),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? '')
    ];

    $result = register_user($user_data, $conn);
    
    if ($result['success']) {
        $success = $result['message'];
        // Redirect to login page after successful registration
        header("Location: login.php?success=" . urlencode($result['message']));
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body.register-page { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url("assets/images/register5.jpg") no-repeat fixed center / cover; margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
.register-main-content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
.site-header, .site-footer { position: relative !important; z-index: 1000; }
.register-container { background-image: linear-gradient(rgba(5,5,5,0.6), rgba(0,0,0,0.7)); padding: 80px 40px; border-radius: 15px; width: 100%; max-width: 400px; box-shadow: 10px 20px 60px rgba(0,0,0,0.3); }
.register-container h2 { margin-bottom: 25px; color: #fcf8f8; text-align: center; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; color: #fdfdfd; font-weight: 500; }
.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #fff; border-radius: 6px; font-size: 15px; }
.form-group input:focus { border-color: #1b5210; outline: none; }
.btn { width: 100%; background-color: #1b4e0e; color: #fff; border: none; padding: 12px; border-radius: 6px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
.btn:hover { background-color: #1f911f; }
.message { background-color: #e6f7ff; color: #32eb2c; border: 1px solid #b3e0ff; padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; text-align: center; }
.error { background-color: #ffe6e6; color: #cc0000; border: 1px solid #ffcccc; padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; text-align: center; }
.login-link { color: #fafafa; text-align: center; margin-top: 15px; font-size: 16px; }
.login-link a { color: #ced10d; text-decoration: none; }
.login-link a:hover { text-decoration: underline; }
@media screen and (max-width: 480px) { .register-container { padding: 30px 20px; } }
</style>
</head>
<body class="register-page">
    <?php include_once 'includes/header.php'; ?>
    
    <div class="register-main-content">
        <div class="register-container">
    <form method="post" action="register.php">
        <h2>Register</h2>

        <?php
        if (!empty($error)) { echo "<div class='error'>$error</div>"; }
        if (!empty($success)) { echo "<div class='message'>$success</div>"; }
        ?>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <input type="submit" name="register-btn" value="Register" class="btn">

        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </form>
        </div>
    </div>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>