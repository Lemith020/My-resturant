<?php
session_start();
include '../includes/db.php';


if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}


$cart_items = $_SESSION['cart'];
$grand_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sun & Sea Restaurant</title>
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

        .checkout-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 25px;
            text-align: center;
        }

        /* 2-Column Grid Layout */
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 25px;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }

        .card-box {
            background: rgba(15, 25, 48, 0.85);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #00ff88;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            background: #060c18;
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }

        .form-group select option {
            background: #060c18;
            color: #ffffff;
        }

        /* Order Summary Items */
        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #cbd5e1;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 700;
            color: #00ff88;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid rgba(0, 255, 136, 0.3);
        }

        /* Confirm Button */
        .btn-confirm {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background: rgba(0, 255, 136, 0.08);
            border: 1.5px solid #00ff88;
            color: #00ff88;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-confirm:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="checkout-wrapper">
        <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> Complete Your Order</h1>

        <form method="POST" action="place_order.php">
            <div class="checkout-grid">
                
                <!-- Customer Details Form -->
                <div class="card-box">
                    <div class="card-title">
                        <i class="fa-solid fa-user-pen"></i> Customer Information
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="customer_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="customer_contact" placeholder="e.g. 0771234567" required>
                    </div>

                    <div class="form-group">
                        <label>Order Type</label>
                        <select name="order_type" id="order_type" onchange="toggleTable()">
                            <option value="Pickup">Takeaway / Pickup</option>
                            <option value="Dine-in">Dine-in</option>
                        </select>
                    </div>

                    <div class="form-group" id="table-field" style="display:none;">
                        <label>Table Number</label>
                        <input type="text" name="table_number" placeholder="Enter Table Number (e.g. T-05)">
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="Card">Credit / Debit Card (Mock Gateway)</option>
                            <option value="Cash">Cash on Pickup / Table</option>
                        </select>
                    </div>
                </div>

              
                <!-- Order Summary Sidebar -->
<div class="card-box" style="height: fit-content;">
    <div class="card-title">
        <i class="fa-solid fa-receipt"></i> Order Summary
    </div>

    <div class="summary-list">
        <?php foreach ($cart_items as $id => $item): 
           
            if (is_array($item)) {
                $item_name = isset($item['name']) ? $item['name'] : 'Item #' . $id;
                $item_price = isset($item['price']) ? $item['price'] : 0;
                $item_qty = isset($item['quantity']) ? $item['quantity'] : 1;
            } else {
                // $_SESSION['cart'][$id] = quantity 
                $item_name = 'Item #' . $id;
                $item_price = 0; 
                $item_qty = (int)$item;
            }

            $item_total = $item_price * $item_qty;
            $grand_total += $item_total;
        ?>
            <div class="summary-item">
                <span><?php echo htmlspecialchars($item_name); ?> × <?php echo $item_qty; ?></span>
                <span>Rs. <?php echo number_format($item_total, 2); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total-row">
        <span>Total Payable:</span>
        <span>Rs. <?php echo number_format($grand_total, 2); ?></span>
    </div>

    <button type="submit" class="btn-confirm">
        <i class="fa-solid fa-circle-check"></i> Confirm & Pay
    </button>
</div>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function toggleTable() {
            const type = document.getElementById('order_type').value;
            document.getElementById('table-field').style.display = (type === 'Dine-in') ? 'block' : 'none';
        }
    </script>
</body>
</html>