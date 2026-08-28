<?php
session_start();
include '../includes/db.php';


if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['order_id'])) {
    $cancel_id = (int)$_GET['order_id'];
    
   
    $check_sql = "SELECT order_status FROM orders WHERE order_id = '$cancel_id'";
    $check_res = mysqli_query($conn, $check_sql);
    $order_data = mysqli_fetch_assoc($check_res);

    if ($order_data && $order_data['order_status'] == 'Pending') {
    
        mysqli_query($conn, "DELETE FROM order_items WHERE order_id = '$cancel_id'");
        
        mysqli_query($conn, "DELETE FROM orders WHERE order_id = '$cancel_id'");

        header("Location: my_orders.php?msg=cancelled");
        exit();
    } else {
        header("Location: my_orders.php?error=cannot_cancel");
        exit();
    }
}


$customer_contact = $_POST['contact'] ?? $_GET['contact'] ?? $_SESSION['customer_contact'] ?? $_COOKIE['customer_contact'] ?? '';

$orders = [];
if (!empty($customer_contact)) {
    $contact_clean = mysqli_real_escape_string($conn, trim($customer_contact));
    setcookie('customer_contact', $contact_clean, time() + (86400 * 30), "/");

    $sql = "SELECT o.*, oi.quantity, oi.price AS item_price, m.name AS item_name 
            FROM orders o
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN menu_items m ON oi.item_id = m.item_id
            WHERE o.customer_contact = '$contact_clean'
            ORDER BY o.created_at DESC";
            
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['order_id'];
        if (!isset($orders[$id])) {
            $orders[$id] = [
                'order_id' => $row['order_id'],
                'created_at' => $row['created_at'],
                'order_type' => $row['order_type'],
                'table_number' => $row['table_number'],
                'total_amount' => $row['total_amount'],
                'payment_status' => $row['payment_status'],
                'order_status' => $row['order_status'],
                'items' => []
            ];
        }
        if ($row['item_name']) {
            $orders[$id]['items'][] = [
                'name' => $row['item_name'],
                'qty' => $row['quantity'],
                'price' => $row['item_price']
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Sun & Sea Restaurant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0b1325; color: #ffffff; min-height: 100vh; }

        .site-header {
            background: #060c18;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
        }

        .site-header .logo { font-size: 20px; font-weight: 700; color: #ffffff; text-decoration: none; }
        .site-nav { display: flex; gap: 15px; }

        .site-nav a {
            color: #00ff88;
            text-decoration: none;
            padding: 8px 16px;
            border: 1.5px solid #00ff88;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .site-nav a:hover, .site-nav a.active {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 15px #00ff88;
        }

        .main-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .page-title { text-align: center; color: #e2e8f0; margin-bottom: 25px; font-size: 26px; }

        .search-card {
            background: rgba(15, 25, 48, 0.8);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.1);
        }

        .search-form { display: flex; gap: 12px; }
        
        .search-form input {
            flex: 1;
            padding: 12px 16px;
            background: #060c18;
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
        }

        .search-form input:focus {
            border-color: #00ff88;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.3);
        }

        .search-form button {
            background: #00ff88;
            color: #0b1325;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-form button:hover {
            background: #33ff99;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
        }

        .order-card {
            background: rgba(15, 25, 48, 0.7);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .order-id { font-size: 16px; font-weight: 700; color: #ffffff; }
        .order-date { font-size: 12px; color: #94a3b8; }

        .item-list { margin-bottom: 15px; }
        .item-row { display: flex; justify-content: space-between; font-size: 14px; color: #cbd5e1; padding: 4px 0; }

        .status-badge {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending { background: rgba(255, 171, 0, 0.15); color: #ffab00; border: 1px solid #ffab00; }
        .badge-preparing { background: rgba(0, 168, 255, 0.15); color: #00a8ff; border: 1px solid #00a8ff; }
        .badge-ready { background: rgba(156, 136, 255, 0.15); color: #9c88ff; border: 1px solid #9c88ff; }
        .badge-completed { background: rgba(0, 255, 136, 0.15); color: #00ff88; border: 1px solid #00ff88; }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 12px;
        }

        .total-price { font-size: 16px; font-weight: 700; color: #00ff88; }

        /* Delete/Cancel Button Styling */
        .btn-delete-order {
            color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            padding: 5px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete-order:hover {
            background: #ff4757;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
        }

        .alert-msg {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success { background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; color: #00ff88; }
        .alert-danger { background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; color: #ff4757; }
    </style>
</head>
<body>

    <header class="site-header">
        <a href="index.php" class="logo">Sun & Sea Restaurant</a>
        <nav class="site-nav">
            <a href="index.php"><i class="fa-solid fa-utensils"></i> Menu</a>
            <a href="my_orders.php" class="active"><i class="fa-solid fa-receipt"></i> My Orders</a>
        </nav>
    </header>

    <div class="main-container">
        <h1 class="page-title"><i class="fa-solid fa-clock-rotate-left"></i> Track Your Orders</h1>

        <!-- Status Notifications -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cancelled'): ?>
            <div class="alert-msg alert-success"><i class="fa-solid fa-circle-check"></i> Order successfully cancelled!</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'cannot_cancel'): ?>
            <div class="alert-msg alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Cannot cancel order because it is already being prepared or completed!</div>
        <?php endif; ?>

        <!-- Search Box -->
        <div class="search-card">
            <form method="POST" class="search-form">
                <input type="text" name="contact" value="<?php echo htmlspecialchars($customer_contact); ?>" placeholder="Enter Phone Number to search..." required>
                <button type="submit"><i class="fa-solid fa-search"></i> Search</button>
            </form>
        </div>

        <!-- Orders List -->
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $ord): 
                $status_class = strtolower($ord['order_status']);
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Order ID: <?php echo $ord['order_id']; ?></span>
                            <span class="order-date">(<?php echo date('M d, Y - h:i A', strtotime($ord['created_at'])); ?>)</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="status-badge badge-<?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($ord['order_status']); ?>
                            </span>

                            <!-- Delete / Cancel Button Icon (Shows only if Pending) -->
                            <?php if ($ord['order_status'] == 'Pending'): ?>
                                <a href="my_orders.php?action=cancel&order_id=<?php echo $ord['order_id']; ?>" 
                                   class="btn-delete-order" 
                                   onclick="return confirm('Are you sure you want to cancel this order?');" 
                                   title="Cancel Order">
                                    <i class="fa-solid fa-trash"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="item-list">
                        <?php foreach ($ord['items'] as $item): ?>
                            <div class="item-row">
                                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['qty']; ?></span>
                                <span>Rs. <?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-footer">
                        <div style="font-size: 13px; color: #94a3b8;">
                            Type: <strong><?php echo htmlspecialchars($ord['order_type']); ?></strong>
                            <?php if ($ord['table_number']): ?>
                                | Table: <strong><?php echo htmlspecialchars($ord['table_number']); ?></strong>
                            <?php endif; ?>
                        </div>
                        <div class="total-price">
                            Total: Rs. <?php echo number_format($ord['total_amount'], 2); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($customer_contact)): ?>
            <p style="text-align: center; color: #94a3b8; margin-top: 20px;">No active orders found for this phone number.</p>
        <?php else: ?>
            <p style="text-align: center; color: #94a3b8; margin-top: 20px;">Please enter your phone number above to view your order history.</p>
        <?php endif; ?>
    </div>

</body>
</html>