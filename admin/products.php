<?php
session_start();
require_once __DIR__ . '/../database/connection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle file upload
function handleImageUpload($file, $product_name) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }
        
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $product_name);
        $filename = $safe_name . '_' . time() . '.' . $file_extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => 'assets/images/' . $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to upload image.'];
        }
    }
    return ['success' => false, 'message' => 'No image uploaded.'];
}

// Handle product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $status = $_POST['status'];
        $image_url = '';
        
        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = handleImageUpload($_FILES['product_image'], $name);
            if ($upload_result['success']) {
                $image_url = $upload_result['filename'];
            } else {
                $message = $upload_result['message'];
            }
        }
        
        if (!isset($message)) {
            $is_active = $status === 'active' ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO products (product_name, description, category_id, price, image_url, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssidsi", $name, $description, $category_id, $price, $image_url, $is_active);
            $stmt->execute();
            $message = "Product added successfully!";
        }
    }
    
    if (isset($_POST['update_product'])) {
        $id = intval($_POST['product_id']);
        $name = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $status = $_POST['status'];
        $image_url = $_POST['current_image'] ?? '';
        
        // Handle image upload if new image is provided
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = handleImageUpload($_FILES['product_image'], $name);
            if ($upload_result['success']) {
                $image_url = $upload_result['filename'];
            } else {
                $message = $upload_result['message'];
            }
        }
        
        if (!isset($message)) {
            $is_active = $status === 'active' ? 1 : 0;
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, category_id = ?, price = ?, image_url = ?, is_active = ? WHERE product_id = ?");
            $stmt->bind_param("ssidsii", $name, $description, $category_id, $price, $image_url, $is_active, $id);
            $stmt->execute();
            $message = "Product updated successfully!";
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $id = intval($_POST['product_id']);
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = "Product deleted successfully!";
    }
}

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id");

// Get all products with category information
$products = $conn->query("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    ORDER BY c.category_id, p.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Ceylon Fresh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, rgba(32, 2, 2, 0.4) 0%, rgba(19, 6, 2, 0.4) 100%), url('../assets/images/checkout.jpg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; color: #333; }
        .header { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav-menu { background: rgba(255, 255, 255, 0.95); padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .nav-menu a { display: inline-block; margin-right: 20px; padding: 10px 20px; background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; text-decoration: none; border-radius: 5px; transition: all 0.3s ease; }
        .nav-menu a:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .message { background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-weight: 500; }
        .form-section { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .form-section h2 { color: #8B0000; margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #8B0000; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .btn { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 12px 25px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .products-table { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: #f8f9fa; color: #8B0000; font-weight: 600; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 0.9rem; font-weight: 500; }
        .status.active { background: #d4edda; color: #155724; }
        .status.inactive { background: #f8d7da; color: #721c24; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } table { font-size: 0.9rem; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Manage Products</h1>
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
        <div class="message">
            <i class="fas fa-check-circle" style="margin-right: 8px; color: #28a745;"></i>
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" id="product_name" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (Rs.)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Region/Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Region</option>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product_image">Product Image</label>
                        <input type="file" id="product_image" name="product_image" accept="image/*">
                        <small style="color: #666; font-size: 0.9rem;">JPG, PNG, GIF, WebP (Max 5MB)</small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="add_product" class="btn">Add Product</button>
            </form>
        </div>

        <div class="products-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>All Products by Region</h2>
                <a href="../product.php" target="_blank" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997); text-decoration: none; padding: 8px 16px; border-radius: 5px; font-size: 0.9rem;">
                    <i class="fas fa-external-link-alt" style="margin-right: 5px;"></i>View Products Page
                </a>
            </div>
            <p style="color: #666; margin-bottom: 20px; font-style: italic;">Products are organized by regions and will appear on the main products page. Updates here will be reflected immediately on the products page.</p>
            
            <?php 
            // Get all categories and their products
            $categories->data_seek(0);
            $all_products = [];
            
            // Group products by category
            while ($product = $products->fetch_assoc()) {
                $all_products[$product['category_name']][] = $product;
            }
            
            // Display each region with its products
            while ($category = $categories->fetch_assoc()): 
                $region_products = $all_products[$category['category_name']] ?? [];
            ?>
                <div class="region-section" style="margin-top: 30px; background: rgba(255, 255, 255, 0.95); border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px);">
                    <div class="region-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="color: #8B0000; margin: 0; padding: 15px 20px; background: linear-gradient(135deg, rgba(139, 0, 0, 0.1), rgba(43, 3, 3, 0.1)); border-radius: 8px; border-left: 4px solid #8B0000;">
                            <?= htmlspecialchars($category['category_name']) ?>
                        </h3>
                        <span style="background: #8B0000; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem;">
                            <?= count($region_products) ?> Product<?= count($region_products) !== 1 ? 's' : '' ?>
                        </span>
                    </div>
                    
                    <?php if (empty($region_products)): ?>
                        <div style="text-align: center; padding: 40px; color: #666; background: rgba(139, 0, 0, 0.05); border-radius: 8px;">
                            <p style="font-size: 1.1rem; margin-bottom: 10px;">No products in this region yet</p>
                            <p style="font-size: 0.9rem;">Add products using the form above to populate this region</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: linear-gradient(135deg, #8B0000, #2b0303ff); color: white;">
                                        <th style="padding: 12px; text-align: left; border-radius: 5px 0 0 0;">ID</th>
                                        <th style="padding: 12px; text-align: left;">Image</th>
                                        <th style="padding: 12px; text-align: left;">Product Name</th>
                                        <th style="padding: 12px; text-align: left;">Description</th>
                                        <th style="padding: 12px; text-align: left;">Price</th>
                                        <th style="padding: 12px; text-align: left;">Status</th>
                                        <th style="padding: 12px; text-align: left;">Created</th>
                                        <th style="padding: 12px; text-align: center; border-radius: 0 5px 0 0;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($region_products as $product): ?>
                                    <tr style="border-bottom: 1px solid #eee; transition: background 0.3s ease;" onmouseover="this.style.background='rgba(139, 0, 0, 0.05)'" onmouseout="this.style.background='transparent'">
                                        <td style="padding: 12px; font-weight: bold; color: #8B0000;"><?= $product['product_id'] ?></td>
                                        <td style="padding: 12px;">
                                            <?php if ($product['image_url']): ?>
                                                <img src="../<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #8B0000;">
                                            <?php else: ?>
                                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f0f0f0, #e0e0e0); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999; border: 2px solid #ddd;">
                                                    No Image
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px; font-weight: 600; color: #333;"><?= htmlspecialchars($product['product_name']) ?></td>
                                        <td style="padding: 12px; color: #666; max-width: 200px;"><?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...</td>
                                        <td style="padding: 12px; font-weight: bold; color: #8B0000;">Rs. <?= number_format($product['price'], 2) ?></td>
                                        <td style="padding: 12px;">
                                            <span class="status <?= $product['is_active'] ? 'active' : 'inactive' ?>" style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px; color: #666; font-size: 0.9rem;"><?= date('M j, Y', strtotime($product['created_at'])) ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($product)) ?>)" class="btn" style="margin-right: 5px; padding: 6px 12px; font-size: 0.85rem;">Edit</button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product? This will remove it from the products page.')">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <button type="submit" name="delete_product" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Edit Product Modal -->
        <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="modal-content" style="background-color: rgba(255, 255, 255, 0.95); margin: 5% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 600px; backdrop-filter: blur(10px);">
                <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                <h2>Edit Product</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <input type="hidden" name="current_image" id="edit_current_image">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_product_name">Product Name</label>
                            <input type="text" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_price">Price (Rs.)</label>
                            <input type="number" id="edit_price" name="price" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_category_id">Region/Category</label>
                            <select id="edit_category_id" name="category_id" required>
                                <?php 
                                $categories->data_seek(0);
                                while ($category = $categories->fetch_assoc()): ?>
                                    <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_product_image">New Product Image (Optional)</label>
                            <input type="file" id="edit_product_image" name="product_image" accept="image/*">
                            <small style="color: #666; font-size: 0.9rem;">Leave empty to keep current image</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="button" onclick="closeEditModal()" class="btn btn-danger" style="margin-right: 10px;">Cancel</button>
                        <button type="submit" name="update_product" class="btn">Update Product</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_product_name').value = product.product_name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_status').value = product.is_active ? 'active' : 'inactive';
            document.getElementById('edit_current_image').value = product.image_url || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        </script>
    </div>
</body>
</html>
