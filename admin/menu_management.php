<?php
include '../includes/auth_check.php';
include '../includes/db.php';



$sql = "SELECT m.*, c.category_name FROM menu_items m
        LEFT JOIN categories c ON m.category_id = c.category_id
        WHERE m.is_available = 1
        ORDER BY m.item_id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Sun & Sea</title>
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

        /* Animated Header Buttons */
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
            max-width: 1100px;
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .top-bar h1 {
            font-size: 24px;
            color: #e2e8f0;
            font-weight: 600;
        }

        /* Charm Add Button */
        .btn-add {
            text-decoration: none;
            color: #0b1325;
            background: #00ff88;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            background: #33ff99;
            box-shadow: 0 0 18px rgba(0, 255, 136, 0.6);
            transform: translateY(-1px);
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
            padding: 12px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #cbd5e1;
        }

        .admin-table tr:hover {
            background-color: rgba(0, 255, 136, 0.03);
        }

        /* Availability Badges */
        .badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        .badge-yes {
            color: #00ff88;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .badge-no {
            color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.3);
        }

        /* Action Buttons */
        .action-btns {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-edit {
            color: #70a1ff;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: color 0.2s;
        }

        .btn-edit:hover {
            color: #1e90ff;
        }

        .btn-delete {
            color: #ff4757;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: color 0.2s;
        }

        .btn-delete:hover {
            color: #ff6b81;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="admin-header">
        <div class="logo-text">Sun & Sea Restaurant - Admin</div>
        <nav class="admin-nav">
            <a href="dashboard.php" class="nav-link nav-btn"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="orders.php" class="nav-link nav-btn"><i class="fa-solid fa-list-check"></i> Orders</a>
            <a href="logout.php" class="nav-link nav-btn logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </header>

    <div class="dashboard-content">
        <div class="top-bar">
            <h1>Menu Management</h1>
            <a href="add_item.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Dish</a>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td style="color: #ffffff; font-weight: 500;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                            <td><?php echo htmlspecialchars($row['dietary_type'] ?? '-'); ?></td>
                            <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $row['is_available'] ? 'badge-yes' : 'badge-no'; ?>">
                                    <?php echo $row['is_available'] ? 'Yes' : 'No'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="btn-edit">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="delete_item.php?id=<?php echo $row['item_id']; ?>" class="btn-delete" onclick="return confirm('Delete this item?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 25px;">No menu items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>