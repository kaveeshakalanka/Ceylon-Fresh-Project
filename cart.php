<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Handle cart operations
if (isset($_GET['action'])) {
    
    // Remove item
    if ($_GET['action'] === 'remove' && isset($_GET['id'])) {
        $remove_id = (int)$_GET['id'];
        $found = false;
        
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $remove_id) {
                unset($_SESSION['cart'][$key]);
                $found = true;
                $_SESSION['cart_message'] = "Item removed from cart!";
                break;
            }
        }
        if ($found) {
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        header("Location: cart.php");
        exit();
    }
    
    // Update quantity
    if ($_GET['action'] === 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
        $update_id = (int)$_GET['id'];
        $new_quantity = (int)$_GET['qty'];
        
        if ($new_quantity <= 0) {
            // Remove item if quantity is 0
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $update_id) { // Use == instead of ===
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart_message'] = "Item removed from cart!";
                    break;
                }
            }
        } else {
            // Update quantity
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $update_id) { // Use == instead of ===
                    $_SESSION['cart'][$key]['quantity'] = $new_quantity;
                    $_SESSION['cart_message'] = "Quantity updated!";
                    break;
                }
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        header("Location: cart.php");
        exit();
    }
    
    // Clear cart
    if ($_GET['action'] === 'clear') {
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = "Cart cleared!";
        header("Location: cart.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Ceylon Fresh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url("assets/images/homepage.jpg") no-repeat fixed center / cover; margin: 0; padding: 0; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 20px; }
        .site-header, .site-footer { position: relative !important; z-index: 1000; }
        .cart-container { max-width: 900px; margin: auto; background: linear-gradient(rgba(235, 235, 235, 0.8), rgba(231, 227, 227, 0.8)); padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #14433e; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        table th { background: #14433e; color: white; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; margin: 2px; }
        .btn-remove { background: #940d1bff; color: white; }
        .btn-remove:hover { background: #c82333; }
        .btn-update { background: #118140ff; color: white; }
        .btn-update:hover { background: #194102ff; }
        .btn-clear { background: #940d1bff; color: white; }
        .btn-clear:hover { background: #d6460dff; }
        .btn-checkout { background: #0ab320ff; color: white; }
        .btn-checkout:hover { background: #098a3fff; }
        .cart-actions { margin-top: 20px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .empty { text-align: center; padding: 40px 20px; color: #666; font-size: 18px; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; text-align: center; }
        .quantity-input { width: 60px; padding: 4px; border: 1px solid #ddd; border-radius: 3px; text-align: center; }
        .quantity-controls { display: flex; align-items: center; justify-content: center; gap: 5px; }
        .test-links { background: #f8f9fa; padding: 10px; margin: 10px 0; border: 1px solid #dee2e6; border-radius: 5px; }
        .test-links a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .test-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <div class="main-content">
        <div class="cart-container">
        <h2>Your Shopping Cart</h2>
        

        <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="message"><?= htmlspecialchars($_SESSION['cart_message']) ?></div>
        <?php unset($_SESSION['cart_message']); endif; ?>

        <?php if (!empty($_SESSION['cart'])): ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price (Rs.)</th>
                <th>Quantity</th>
                <th>Subtotal (Rs.)</th>
                <th>Actions</th>
            </tr>
            <?php 
            $grand_total = 0; 
            foreach ($_SESSION['cart'] as $item): 
                $subtotal = $item['price'] * $item['quantity']; 
                $grand_total += $subtotal; 
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td>
                    <div class="quantity-controls">
                        <input type="number" id="qty_<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" min="1" max="99" class="quantity-input">
                        <button onclick="updateQuantity(<?= $item['id'] ?>)" class="btn btn-update">Update</button>
                    </div>
                </td>
                <td><?= number_format($subtotal, 2) ?></td>
                <td>
                    <button onclick="removeItem(<?= $item['id'] ?>)" class="btn btn-remove">Remove</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Grand Total</strong></td>
                <td colspan="2"><strong>Rs. <?= number_format($grand_total, 2) ?></strong></td>
            </tr>
        </table>

        <div class="cart-actions">
            <button onclick="clearCart()" class="btn btn-clear">Clear Cart</button>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>

        <?php else: ?>
        <div class="empty">
            Your cart is empty.<br>
            <a href="product.php" class="btn btn-checkout" style="margin-top:10px;">Shop Products</a>
        </div>
        <?php endif; ?>
        </div>
    </div>
    
    <?php include_once 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>