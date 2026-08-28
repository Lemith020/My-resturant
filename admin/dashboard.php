<?php
session_start();
require_once '../includes/db.php';

// Data Fetching
$items_count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM menu_items");
$total_items = mysqli_fetch_assoc($items_count_res)['total'] ?? 0;

$active_orders_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE order_status != 'Completed'");
$active_orders = mysqli_fetch_assoc($active_orders_res)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sun & Sea Restaurant - Admin</title>
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

        /* Header Style */
        .admin-header {
            background: #060c18;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            box-shadow: 0 4px 20px rgba(0, 255, 136, 0.05);
        }

        .admin-header .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .admin-nav {
            display: flex;
            gap: 15px;
        }

        /* Glowing Animated Header Buttons */
        .admin-nav .nav-btn {
            text-decoration: none;
            color: #00ff88;
            padding: 10px 22px;
            border: 2px solid #00ff88;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
        }

        .admin-nav .nav-btn:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 20px #00ff88, 0 0 40px #00ff88;
            transform: translateY(-2px);
        }

        .admin-nav .nav-btn.logout-btn {
            color: #ff4757;
            border-color: #ff4757;
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.2);
        }

        .admin-nav .nav-btn.logout-btn:hover {
            background: #ff4757;
            color: #ffffff;
            box-shadow: 0 0 20px #ff4757;
        }

        /* Dashboard Container */
        .dashboard-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 40px;
            color: #e2e8f0;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        /* Cards Layout */
        .cards-wrapper {
            display: flex;
            gap: 35px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Luminous Glowing Cards */
        .dash-card {
            background: rgba(15, 25, 48, 0.8);
            width: 250px;
            padding: 35px 25px;
            border-radius: 20px;
            border: 2px solid #00ff88;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
            animation: cardPulse 3s infinite alternate;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .dash-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 0 30px #00ff88, 0 0 50px rgba(0, 255, 136, 0.5);
            background: rgba(15, 25, 48, 1);
        }

        .card-number {
            font-size: 48px;
            font-weight: 800;
            color: #00ff88;
            margin-bottom: 10px;
            text-shadow: 0 0 15px rgba(0, 255, 136, 0.6);
        }

        .card-label {
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Luminous Pulse Animation */
        @keyframes cardPulse {
            0% {
                box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
                border-color: #00ff88;
            }
            100% {
                box-shadow: 0 0 25px rgba(0, 255, 136, 0.5), 0 0 40px rgba(0, 255, 136, 0.3);
                border-color: #33ff99;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="admin-header">
        <div class="logo-text">Sun & Sea Restaurant - Admin</div>
        <nav class="admin-nav">
            <a href="menu_management.php" class="nav-link nav-btn"><i class="fa-solid fa-utensils"></i> Menu</a>
            <a href="orders.php" class="nav-link nav-btn"><i class="fa-solid fa-list-check"></i> Orders</a>
            <a href="logout.php" class="nav-link nav-btn logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </header>

    <!-- Main Centered Content -->
    <div class="dashboard-container">
        <h1 class="welcome-title">Welcome, admin</h1>

        <div class="cards-wrapper">
            <!-- Menu Items Card -->
            <div class="dash-card" onclick="window.location.href='menu_management.php'">
                <div class="card-number"><?php echo $total_items; ?></div>
                <div class="card-label">MENU ITEMS</div>
            </div>

            <!-- Active Orders Card -->
            <div class="dash-card" onclick="window.location.href='orders.php'">
                <div class="card-number"><?php echo $active_orders; ?></div>
                <div class="card-label">ACTIVE ORDERS</div>
            </div>
        </div>
    </div>

</body>
</html>