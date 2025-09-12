<?php
// DB configure
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  
define('DB_NAME', 'ceylonfresh');

// Create connection using MySQLi
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");


function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

//  if user is logged in
function check_login() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

//  if user is admin
function check_admin() {
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] != 'admin') {
        header("Location: login.php");
        exit();
    }
}

//  get user data by ID
function get_user_by_id($user_id) {
    global $conn;
    $user_id = sanitize_input($user_id);
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

//  get all categories
function get_all_categories() {
    global $conn;
    $sql = "SELECT * FROM categories ORDER BY category_name";
    $result = $conn->query($sql);
    return $result;
}

// get products by category
function get_products_by_category($category_id) {
    global $conn;
    $category_id = sanitize_input($category_id);
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.category_id = '$category_id' AND p.is_active = 1 
            ORDER BY p.product_name";
    $result = $conn->query($sql);
    return $result;
}

//  get all active products
function get_all_active_products() {
    global $conn;
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.is_active = 1 
            ORDER BY c.category_name, p.product_name";
    $result = $conn->query($sql);
    return $result;
}

//  get product by ID
function get_product_by_id($product_id) {
    global $conn;
    $product_id = sanitize_input($product_id);
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = '$product_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

// Close connection   
function close_connection() {
    global $conn;
    $conn->close();
}

?>