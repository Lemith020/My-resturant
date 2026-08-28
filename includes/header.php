<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern Dark Navigation Header */
        .main-navbar {
            background-color: #060c18;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            position: relative;
            z-index: 1000;
        }

        .main-navbar .logo {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-navbar .logo i {
            color: #00ff88;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: #00ff88;
            text-decoration: none;
            padding: 8px 16px;
            border: 1.5px solid #00ff88;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 255, 136, 0.05);
        }

        .nav-links a:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 12px rgba(0, 255, 136, 0.4);
            transform: translateY(-2px);
        }

        .nav-links a.active {
            background: #00ff88;
            color: #0b1325;
        }

        /* Cart Badge */
        .cart-badge {
            background: #ff4757;
            color: #ffffff;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 50%;
            margin-left: 4px;
        }
    </style>
</head>
<body>

<header class="main-navbar">
    <a href="index.php" class="logo">
        <i class="fa-solid fa-umbrella-beach"></i> Sun & Sea Restaurant
    </a>

    <nav>
        <div class="nav-links">
            <a href="index.php"><i class="fa-solid fa-utensils"></i> Menu</a>
            <a href="cart.php">
                <i class="fa-solid fa-cart-shopping"></i> Cart 
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                <?php endif; ?>
            </a>
            <a href="favorites.php"><i class="fa-solid fa-heart"></i> Favorites</a>
            <a href="my_orders.php"><i class="fa-solid fa-receipt"></i> My Orders</a>
        </div>
    </nav>
</header>