<?php
session_start();
require_once __DIR__ . '/../database/connection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $message = "Order status updated successfully!";
}

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    
    // First delete order items
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Then delete the order
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $message = "Order deleted successfully!";
}

// Get all orders with user information
$orders = $conn->query("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Ceylon Fresh</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, rgba(32, 2, 2, 0.4) 0%, rgba(19, 6, 2, 0.4) 100%), url('../assets/images/checkout.jpg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; color: #333; }
        .header { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav-menu { background: rgba(255, 255, 255, 0.95); padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .nav-menu a { display: inline-block; margin-right: 20px; padding: 10px 20px; background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; text-decoration: none; border-radius: 5px; transition: all 0.3s ease; }
        .nav-menu a:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .orders-table { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: #f8f9fa; color: #8B0000; font-weight: 600; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 0.9rem; font-weight: 500; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.processing { background: #cce5ff; color: #004085; }
        .status.shipped { background: #fff3cd; color: #856404; }
        .status.delivered { background: #d4edda; color: #155724; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .btn { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 8px 15px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 5px 10px; font-size: 12px; }
        .order-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; font-size: 0.9rem; }
        .order-details h4 { color: #8B0000; margin-bottom: 10px; }
        .order-details p { margin-bottom: 5px; }
        @media (max-width: 768px) { table { font-size: 0.9rem; } .nav-menu a { display: block; margin-bottom: 10px; margin-right: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Manage Orders</h1>
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

        <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="orders-table">
            <h2>All Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
                        <td><?= htmlspecialchars($order['email'] ?? 'N/A') ?></td>
                        <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                        <td><span class="status <?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></td>
                        <td><?= date('M j, Y H:i', strtotime($order['order_date'])) ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 5px; border-radius: 3px; border: 1px solid #ddd;">
                                    <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                            <form method="POST" style="display: inline; margin-left: 10px;" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger btn-small">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div class="order-details">
                                <h4>Order Details</h4>
                                <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                                <p><strong>Order Items:</strong></p>
                                <?php
                                // Get order items
                                $items_query = $conn->prepare("
                                    SELECT oi.*, p.product_name 
                                    FROM order_items oi 
                                    LEFT JOIN products p ON oi.product_id = p.product_id 
                                    WHERE oi.order_id = ?
                                ");
                                $items_query->bind_param("i", $order['id']);
                                $items_query->execute();
                                $items = $items_query->get_result();
                                
                                while ($item = $items->fetch_assoc()):
                                ?>
                                <p style="margin-left: 20px;">
                                    <?= htmlspecialchars($item['product_name']) ?> - 
                                    Qty: <?= $item['quantity'] ?> - 
                                    Rs. <?= number_format($item['unit_price'], 2) ?> each
                                </p>
                                <?php endwhile; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
