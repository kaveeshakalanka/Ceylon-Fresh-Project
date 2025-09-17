<?php
session_start();
require_once __DIR__ . '/../database/connection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_level = 'user'");
$stats['users'] = $result->fetch_assoc()['count'];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status = 'completed'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ceylon Fresh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, rgba(32, 2, 2, 0.4) 0%, rgba(19, 6, 2, 0.4) 100%), url('../assets/images/checkout.jpg'); 
            background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; color: #333; }
        .header { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .logout-btn { background: rgba(255,255,255,0.2) !important; color: white !important; padding: 10px 20px !important; border-radius: 5px !important; 
            text-decoration: none !important; transition: all 0.3s ease !important; display: inline-flex !important; 
            align-items: center !important; gap: 8px !important; border: 1px solid rgba(255,255,255,0.3) !important; }
        .logout-btn:hover { background: rgba(255,255,255,0.3) !important; transform: translateY(-2px) !important; box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav-menu { background: rgba(255, 255, 255, 0.95); padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .nav-menu a { display: inline-block; margin-right: 20px; padding: 10px 20px; background: linear-gradient(180deg, #8B0000, #2b0303ff); 
            color: white; text-decoration: none; border-radius: 5px; transition: all 0.3s ease; }
        .nav-menu a:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; transition: all 0.3s ease; backdrop-filter: blur(10px); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .stat-card h3 { color: #570303ff; font-size: 2.5rem; margin-bottom: 10px; }
        .stat-card p { color: #666; font-size: 1.1rem; }
        .recent-orders { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .recent-orders h2 { color: #570303ff; margin-bottom: 20px; font-size: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: #f8f9fa; color: #570303ff; font-weight: 600; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 0.9rem; font-weight: 500; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.completed { background: #d4edda; color: #155724; }
        .status.processing { background: #cce5ff; color: #004085; }
        @media (max-width: 768px) { .nav-menu a { display: block; margin-bottom: 10px; margin-right: 0; } .stats-grid { grid-template-columns: 1fr; } table { font-size: 0.9rem; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!</p>
                    <p style="font-size: 14px; opacity: 0.8;">Admin Dashboard</p>
                </div>
                <div>
                    <a href="../login.php?logout=1" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="nav-menu">
            <a href="dashboad.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="users.php">Users</a>
            <a href="../index.php">Back to Site</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $stats['users'] ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['products'] ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['orders'] ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3>Rs. <?= number_format($stats['revenue'], 2) ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
                        <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                        <td><span class="status <?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></td>
                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
