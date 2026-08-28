<?php
include '../includes/auth_check.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['order_status'];
    
    $stmt = mysqli_prepare($conn, "UPDATE orders SET order_status = ? WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: orders.php?msg=updated");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Sun & Sea </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0b1325;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Header Style */
        .admin-header {
            background: #060c18;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            box-shadow: 0 4px 20px rgba(0, 255, 136, 0.05);
        }

        .admin-header .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .admin-nav {
            display: flex;
            gap: 12px;
        }

        .admin-nav .nav-btn {
            text-decoration: none;
            color: #00ff88;
            padding: 8px 18px;
            border: 1.5px solid #00ff88;
            border-radius: 25px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-nav .nav-btn:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 15px #00ff88;
            transform: translateY(-2px);
        }

        .admin-nav .nav-btn.logout-btn {
            color: #ff4757;
            border-color: #ff4757;
            box-shadow: 0 0 8px rgba(255, 71, 87, 0.15);
        }

        .admin-nav .nav-btn.logout-btn:hover {
            background: #ff4757;
            color: #ffffff;
            box-shadow: 0 0 15px #ff4757;
        }

        /* Main Container */
        .dashboard-content {
            padding: 35px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-bar {
            margin-bottom: 25px;
        }

        .top-bar h1 {
            font-size: 24px;
            color: #e2e8f0;
            font-weight: 600;
        }

        /* Luminous Table Container */
        .table-wrapper {
            background: rgba(15, 25, 48, 0.7);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .admin-table th {
            background-color: rgba(6, 12, 24, 0.85);
            color: #00ff88;
            font-weight: 600;
            padding: 14px 18px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
        }

        .admin-table td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #cbd5e1;
            vertical-align: middle;
        }

        .admin-table tr:hover {
            background-color: rgba(0, 255, 136, 0.03);
        }

        /* Status Select & Button */
        .status-select {
            background: #060c18;
            color: #ffffff;
            border: 1px solid rgba(0, 255, 136, 0.3);
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
            outline: none;
        }

        .btn-update {
            background: #00ff88;
            color: #0b1325;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.2);
        }

        .btn-update:hover {
            background: #33ff99;
            box-shadow: 0 0 12px rgba(0, 255, 136, 0.5);
        }

        /* Status Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pending { background: rgba(255, 171, 0, 0.15); color: #ffab00; border: 1px solid #ffab00; }
        .badge-preparing { background: rgba(0, 168, 255, 0.15); color: #00a8ff; border: 1px solid #00a8ff; }
        .badge-ready { background: rgba(156, 136, 255, 0.15); color: #9c88ff; border: 1px solid #9c88ff; }
        .badge-completed { background: rgba(0, 255, 136, 0.15); color: #00ff88; border: 1px solid #00ff88; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="admin-header">
        <div class="logo-text">Sun & Sea Restaurant - Admin</div>
        <nav class="admin-nav">
            <a href="dashboard.php" class="nav-link nav-btn"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="menu_management.php" class="nav-link nav-btn"><i class="fa-solid fa-utensils"></i> Menu</a>
            <a href="logout.php" class="nav-link nav-btn logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </header>

    <div class="dashboard-content">
        <div class="top-bar">
            <h1><i class="fa-solid fa-list-check"></i> Incoming Orders</h1>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Table</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Current Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($result)) { 
                            $status_class = strtolower($order['order_status']);
                        ?>
                        <tr>
                            <td style="color: #00ff88; font-weight: 700;"><?php echo $order['order_id']; ?></td>
                            <td>
                                <strong style="color: #fff;"><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <small style="color: #94a3b8;"><?php echo htmlspecialchars($order['customer_contact']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                            <td><?php echo !empty($order['table_number']) ? htmlspecialchars($order['table_number']) : '-'; ?></td>
                            <td style="color: #00ff88; font-weight: 600;">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:flex; gap:8px; align-items:center;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="order_status" class="status-select">
                                        <option value="Pending" <?php echo $order['order_status']=='Pending'?'selected':''; ?>>Pending</option>
                                        <option value="Preparing" <?php echo $order['order_status']=='Preparing'?'selected':''; ?>>Preparing</option>
                                        <option value="Ready" <?php echo $order['order_status']=='Ready'?'selected':''; ?>>Ready</option>
                                        <option value="Completed" <?php echo $order['order_status']=='Completed'?'selected':''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn-update">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 25px;">No orders available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>