<?php
session_start();
require_once '../includes/db.php';

// Cart Session Check & Data Fetching
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$items = [];
$total = 0;

if (!empty($cart_items)) {
    $ids = implode(',', array_map('intval', array_keys($cart_items)));
    
    $query = "SELECT * FROM menu_items WHERE item_id IN ($ids)";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['item_id'];
        $cart_value = $cart_items[$id];

        
        $qty = is_array($cart_value) ? (int)($cart_value['quantity'] ?? 1) : (int)$cart_value;
        $price = (float)$row['price'];

        $subtotal = $price * $qty;
        $total += $subtotal;

        $row['quantity'] = $qty;
        $row['subtotal'] = $subtotal;
        $items[] = $row;
    }
}


$total_cart_count = !empty($cart_items) ? array_sum(array_map(function($val) {
    return is_array($val) ? ($val['quantity'] ?? 1) : $val;
}, $cart_items)) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Sun & Sea Restaurant</title>
    <!-- FontAwesome Icon Link -->
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
            display: flex;
            flex-direction: column;
        }

        /* Site Header Navigation */
        .site-header {
            background-color: #060c18;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }

        .site-header .logo a {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
        }

        .site-header nav {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-link {
            color: #00ff88;
            text-decoration: none;
            padding: 8px 16px;
            border: 1.5px solid #00ff88;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(0, 255, 136, 0.05);
        }

        .nav-link:hover, .cart-btn {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 12px rgba(0, 255, 136, 0.4);
        }

        /* Main Container */
        .main-container {
            padding: 40px 20px;
            max-width: 950px;
            margin: 0 auto;
            flex: 1;
            width: 100%;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 25px;
            text-align: center;
        }

        /* Card Container Styling */
        .card-box {
            background: rgba(15, 25, 48, 0.85);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .empty-msg {
            font-size: 16px;
            color: #cbd5e1;
            text-align: center;
            padding: 20px 0;
        }

        .empty-msg a {
            color: #00ff88;
            font-weight: bold;
            text-decoration: none;
        }

        .empty-msg a:hover {
            text-decoration: underline;
        }

        /* Table Styling */
        .cart-table-wrapper {
            overflow-x: auto;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .cart-table th {
            padding: 14px;
            color: #00ff88;
            font-size: 14px;
            border-bottom: 1px solid rgba(0, 255, 136, 0.3);
            font-weight: 700;
        }

        .cart-table td {
            padding: 14px;
            color: #cbd5e1;
            font-size: 14px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
            vertical-align: middle;
        }

        .item-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(0, 255, 136, 0.2);
        }

        .btn-trash {
            background: none;
            border: none;
            color: #ff4757;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .btn-trash:hover {
            color: #ff6b81;
            transform: scale(1.15);
        }

        /* Footer & Total Section */
        .cart-summary {
            margin-top: 25px;
            text-align: right;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
        }

        .cart-summary h2 {
            font-size: 20px;
            color: #ffffff;
            font-weight: 700;
        }

        .cart-summary h2 span {
            color: #00ff88;
        }

        .btn-checkout {
            margin-top: 15px;
            padding: 12px 28px;
            background: rgba(0, 255, 136, 0.08);
            border: 1.5px solid #00ff88;
            color: #00ff88;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-checkout:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
            transform: translateY(-2px);
        }

        /* Site Footer */
        .site-footer {
            background-color: #060c18;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px;
            text-align: center;
            color: #cbd5e1;
            font-size: 13px;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Site Header -->
    <header class="site-header">
        <div class="logo">
            <a href="index.php"><i class="fa-solid fa-umbrella-beach" style="color:#00ff88;"></i> Sun & Sea Restaurant</a>
        </div>
        <nav>
            <a href="index.php" class="nav-link">Home</a>
            <a href="favorites.php" class="nav-link">Favorites ❤️</a>
            <a href="cart.php" class="nav-link cart-btn">Cart (<span id="cart-count"><?php echo $total_cart_count; ?></span>)</a>
        </nav>
    </header>

    <!-- Main Content Container -->
    <div class="main-container">
        <h1 class="page-title"><i class="fa-solid fa-cart-shopping"></i> Your Cart</h1>

        <div class="card-box">
            <?php if (empty($items)) : ?>
                <p class="empty-msg">Your cart is empty. <a href="index.php">Browse the menu</a></p>
            <?php else : ?>

                <!-- Table Scroll Wrapper -->
                <div class="cart-table-wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr id="cart-item-<?php echo $item['item_id']; ?>">
                                    <td>
                                        <img src="../assets/images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="item-img"
                                             onerror="this.src='../assets/images/default.jpg';">
                                    </td>
                                    <td style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td style="color: #00ff88; font-weight: 600;">Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td style="text-align: center;">
                                        <button type="button" 
                                                class="btn-trash"
                                                onclick="removeFromCart(<?php echo $item['item_id']; ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h2>Total: <span>Rs. <?php echo number_format($total, 2); ?></span></h2>
                    <a href="checkout.php" style="text-decoration: none;">
                        <button class="btn-checkout">
                            Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </a>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Site Footer -->
    <footer class="site-footer">
        <p>&copy; <?php echo date('Y'); ?> Sun & Sea Restaurant. All rights reserved.</p>
    </footer>

    <!-- Delete Item JavaScript -->
    <script>
    function removeFromCart(itemId) {
        if (confirm('Do you want to remove this item from the cart?')) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_id: parseInt(itemId) })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload(); // Page එක reload වී item එක ඉවත් වේ
                } else {
                    alert('Failed to remove item: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Something went wrong!');
            });
        }
    }
    </script>

</body>
</html>