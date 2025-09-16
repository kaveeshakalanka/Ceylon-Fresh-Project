<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once _DIR_ . '/database/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart if empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    echo "<p style='text-align:center;margin-top:50px;font-size:20px;'>Your cart is empty. <a href='product.php'>Shop Now</a></p>";
    exit();
}

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $payment_method = sanitize_input($_POST['payment_method']);
    $shipping_address = sanitize_input($_POST['shipping_address']);
    $special_instructions = sanitize_input($_POST['special_instructions'] ?? '');
    
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $delivery_fee = 150.00;
    $total_amount = $subtotal + $delivery_fee;

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, order_status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("ids", $user_id, $total_amount, $shipping_address);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }

    // Clear cart
    $_SESSION['cart'] = [];

    echo "<script>alert('Order placed successfully! Order ID: #$order_id'); window.location.href='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Ceylon Fresh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, rgba(245, 247, 250, 0.8) 0%, rgba(195, 207, 226, 0.8) 100%), 
            url('assets/images/checkout.jpg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .checkout-container { max-width: 1200px; margin: 20px auto; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; flex: 1; }
        .checkout-header { background: linear-gradient(135deg, #2e8b57, #228b22); color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; }
        .checkout-header h2 { margin: 0; font-size: 28px; font-weight: 600; }
        .back-to-cart-btn { background: rgba(255,255,255,0.2); color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; 
            transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.3); }
        .back-to-cart-btn:hover { background: rgba(255,255,255,0.3); transform: translateY(-2px); }
        .checkout-content { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; padding: 30px; }
        .customer-info h3, 
        .order-summary h3 { color: #2e8b57; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; }
        .form-group input, 
        .form-group select, 
        .form-group textarea { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 15px; 
            transition: border-color 0.3s ease; box-sizing: border-box; }
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus { outline: none; border-color: #2e8b57; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .checkout-buttons { margin-top: 30px; }
        .btn-primary { width: 100%; padding: 15px 20px; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600; transition: all 0.3s ease; border: none; cursor: pointer; 
            font-size: 16px; background: linear-gradient(135deg, #28a745, #20c997); color: white; }
        .btn-primary:hover { background: linear-gradient(135deg, #218838, #1ea085); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3); }
        .order-summary { background: #f8f9fa; padding: 25px; border-radius: 12px; height: fit-content; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        table th { background: #e9ecef; font-weight: 600; color: #495057; }
        .order-totals { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 5px 0; }
        .final-total { font-weight: bold; font-size: 18px; color: #2e8b57; border-top: 2px solid #e9ecef; padding-top: 15px; margin-top: 15px; }
        .delivery-info { background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #2e8b57; }
        .delivery-info h4 { color: #2e8b57; margin-bottom: 15px; }
        .delivery-info p { margin-bottom: 10px; color: #6c757d; }
        .delivery-info i { color: #2e8b57; margin-right: 8px; }
        .site-footer { position: relative !important; z-index: 1000; }
        @media (max-width: 768px) { 
        .checkout-content { grid-template-columns: 1fr; padding: 20px; } 
        .form-row { grid-template-columns: 1fr; } 
        .checkout-header { flex-direction: column; gap: 15px; text-align: center; } }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="checkout-container">
        <div class="checkout-header">
            <h2><i class="fas fa-shopping-cart"></i> Checkout</h2>
            <a href="cart.php" class="back-to-cart-btn">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </a>
        </div>

        <div class="checkout-content">
            <div class="checkout-left">
                <div class="customer-info">
                    <h3><i class="fas fa-user"></i> Customer Information</h3>
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" 
                                       value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash_on_delivery">Cash on Delivery</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="card_payment">Card Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_address">Shipping Address</label>
                            <textarea id="shipping_address" name="shipping_address" 
                                      placeholder="Enter your complete shipping address..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions (Optional)</label>
                            <textarea id="special_instructions" name="special_instructions" 
                                      placeholder="Any special delivery instructions..."></textarea>
                        </div>
                        <div class="checkout-buttons">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-check"></i> Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="checkout-right">
                <div class="order-summary">
                    <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                    <div class="cart-summary">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th width="100">Price (Rs.)</th>
                        <th width="80">Qty</th>
                        <th width="120">Subtotal (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $subtotal += $item_subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item_subtotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>Rs. <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Delivery Fee:</span>
                    <span>Rs. 150.00</span>
                </div>
                <div class="total-row final-total">
                    <span>Total:</span>
                    <span>Rs. <?= number_format($subtotal + 150, 2) ?></span>
                </div>
            </div>
            
            <div class="delivery-info">
                <h4><i class="fas fa-truck"></i> Delivery Information</h4>
                <p><i class="fas fa-clock"></i> Estimated delivery: 2-3 business days</p>
                <p><i class="fas fa-map-marker-alt"></i> Free delivery on orders over Rs. 1000</p>
            </div>
        </div>
    </div>
    </div>
    
   
    <script src="assets/js/script.js"></script>
</body>
</html>