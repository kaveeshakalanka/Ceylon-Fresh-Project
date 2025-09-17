<?php
 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

 
  // Check if user is admin
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin';
}

/**
 * Check if user is customer
 * @return bool True if user is customer, false otherwise
 */
function is_customer() {
    return is_logged_in() && isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'customer';
}

/**
 * Get current user data
 * @return array|false User data array or false if not logged in
 */
function get_current_user_data() {
    if (!is_logged_in()) {
        return false;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'user_level' => $_SESSION['user_level'] ?? 'customer',
        'full_name' => $_SESSION['full_name'] ?? '',
        'phone' => $_SESSION['phone'] ?? '',
        'address' => $_SESSION['address'] ?? ''
    ];
}

 
 // Require user to be logged in
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

 
  // Require user to be admin
function require_admin() {
    if (!is_admin()) {
        if (!is_logged_in()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header("Location: login.php");
        } else {
            header("Location: index.php?error=access_denied");
        }
        exit();
    }
}

/**
 * Authenticate user login
 * @param string $email User email
 * @param string $password User password
 * @param mysqli $conn Database connection
 * @return array|false User data if successful, false if failed
 */
function authenticate_user($email, $password, $conn) {
    // Only trim the email, don't apply htmlspecialchars for database lookup
    $email = trim($email);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    
    return false;
}

/**
 * Register new user
 * @param array $user_data User registration data
 * @param mysqli $conn Database connection
 * @return array Result array with success status and message
 */
function register_user($user_data, $conn) {
    $username = sanitize_input($user_data['username']);
    $email = sanitize_input($user_data['email']);
    $password = $user_data['password'];
    $full_name = sanitize_input($user_data['full_name']);
    $phone = sanitize_input($user_data['phone'] ?? '');
    $address = sanitize_input($user_data['address'] ?? '');
    
    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        return ['success' => false, 'message' => 'All required fields must be filled.'];
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format.'];
    }
    
    // Validate password strength
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters long.'];
    }
    
    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already exists.'];
    }
    
    // Check if username already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists.'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_level, full_name, phone, address) VALUES (?, ?, ?, 'customer', ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $address);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registration successful! You can now login.'];
    } else {
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Set user session after successful login
 * @param array $user User data from database
 */
function set_user_session($user) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['username'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_level'] = $user['user_level'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['phone'] = $user['phone'] ?? '';
    $_SESSION['address'] = $user['address'] ?? '';
    $_SESSION['login_time'] = time();
}

/**
 * Clear user session (logout)
 */
function clear_user_session() {
   $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Redirect after login
 * Redirects to the page user was trying to access before login
 */
function redirect_after_login() {
    $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect_url");
    exit();
}

/**
 * Get user permissions
 * @return array Array of user permissions
 */
function get_user_permissions() {
    if (!is_logged_in()) {
        return ['view_products', 'view_cart'];
    }
    
    $permissions = ['view_products', 'view_cart', 'add_to_cart', 'place_order'];
    
    if (is_admin()) {
        $permissions = array_merge($permissions, [
            'admin_dashboard',
            'manage_products',
            'manage_orders',
            'manage_users'
        ]);
    }
    
    return $permissions;
}

/**
 * Check if user has specific permission
 * @param string $permission Permission to check
 * @return bool True if user has permission, false otherwise
 */
function has_permission($permission) {
    $permissions = get_user_permissions();
    return in_array($permission, $permissions);
}
?>