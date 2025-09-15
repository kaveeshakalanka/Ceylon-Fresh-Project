<?php
session_start();
require_once _DIR_ . '/../database/connection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $user_id = intval($_POST['user_id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $user_type = $_POST['user_type'] ?? 'customer';
        
        // Validate user_type
        if (!in_array($user_type, ['admin', 'customer'])) {
            $user_type = 'customer';
        }
        
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, user_level = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $email, $user_type, $user_id);
        $stmt->execute();
        $message = "User updated successfully!";
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND user_level != 'admin'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $message = "User deleted successfully!";
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Ceylon Fresh</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, rgba(32, 2, 2, 0.4) 0%, rgba(19, 6, 2, 0.4) 100%), url('../assets/images/checkout.jpg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; color: #333; }
        .header { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav-menu { background: rgba(255, 255, 255, 0.95); padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        .nav-menu a { display: inline-block; margin-right: 20px; padding: 10px 20px; background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; text-decoration: none; border-radius: 5px; transition: all 0.3s ease; }
        .nav-menu a:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .users-table { background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        table th { background: #f8f9fa; color: #8B0000; font-weight: 600; }
        .user-type { padding: 5px 10px; border-radius: 15px; font-size: 0.9rem; font-weight: 500; }
        .user-type.admin { background: #d1ecf1; color: #0c5460; }
        .user-type.customer { background: #d4edda; color: #155724; }
        .btn { background: linear-gradient(180deg, #8B0000, #2b0303ff); color: white; padding: 8px 15px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { background: linear-gradient(180deg, #8B0000, #2b0303ff); transform: translateY(-2px); }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 5px 10px; font-size: 12px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #8B0000; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .edit-form { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; display: none; }
        .edit-form.active { display: block; }
        @media (max-width: 768px) { table { font-size: 0.9rem; } .nav-menu a { display: block; margin-bottom: 10px; margin-right: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Manage Users</h1>
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

        <div class="users-table">
            <h2>All Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="user-type <?= $user['user_level'] ?>"><?= ucfirst($user['user_level']) ?></span></td>
                        <td><span class="status active">Active</span></td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <button onclick="toggleEditForm(<?= $user['user_id'] ?>)" class="btn btn-small">Edit</button>
                            <?php if ($user['user_level'] !== 'admin'): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-small">Delete</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div class="edit-form" id="edit-form-<?= $user['user_id'] ?>">
                                <h4>Edit User</h4>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="user_type">User Type</label>
                                        <select id="user_type" name="user_type">
                                            <option value="customer" <?= $user['user_level'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                            <option value="admin" <?= $user['user_level'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_user" class="btn">Update User</button>
                                    <button type="button" onclick="toggleEditForm(<?= $user['user_id'] ?>)" class="btn btn-danger">Cancel</button>
                                </form>
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