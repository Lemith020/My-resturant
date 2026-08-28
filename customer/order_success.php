<?php
session_start();
$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="success-box">
        <h1>Thank you!</h1>
        <p>Your order #<?php echo $order_id; ?> has been placed and paid successfully.</p>
        <a href="index.php">Back to Menu</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
