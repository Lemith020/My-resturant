<?php
session_start();
include '../includes/db.php';

$_SESSION['customer_contact'] = $_POST['customer_contact'];
setcookie('customer_contact', $_POST['customer_contact'], time() + (86400 * 30), "/");

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
$customer_contact = mysqli_real_escape_string($conn, $_POST['customer_contact']);
$order_type = $_POST['order_type'];
$table_number = isset($_POST['table_number']) ? mysqli_real_escape_string($conn, $_POST['table_number']) : null;

$cart = $_SESSION['cart'];
$ids = implode(',', array_map('intval', array_keys($cart)));
$result = mysqli_query($conn, "SELECT * FROM menu_items WHERE item_id IN ($ids)");

$total = 0;
$order_items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $item_id = $row['item_id'];
    $cart_val = $cart[$item_id];


    $qty = is_array($cart_val) ? (int)($cart_val['quantity'] ?? 1) : (int)$cart_val;
    $price = (float)$row['price'];

    $subtotal = $qty * $price;
    $total += $subtotal;

    $order_items[] = [
        'item_id' => $item_id, 
        'quantity' => $qty, 
        'price' => $price
    ];
}

// 1. orders table insert
$sql = "INSERT INTO orders (order_type, table_number, customer_name, customer_contact, total_amount, payment_status, order_status)
        VALUES (?, ?, ?, ?, ?, 'Paid', 'Pending')";
$stmt = mysqli_prepare($conn, $sql);

// Datatypes: s=string, d=double/float -> "ssssd"
mysqli_stmt_bind_param($stmt, "ssssd", $order_type, $table_number, $customer_name, $customer_contact, $total);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);


$stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");

foreach ($order_items as $oi) {
    // Datatypes: i=integer, d=double/float -> "iiid"
    mysqli_stmt_bind_param($stmt2, "iiid", $order_id, $oi['item_id'], $oi['quantity'], $oi['price']);
    mysqli_stmt_execute($stmt2);
}
mysqli_stmt_close($stmt2);


$_SESSION['cart'] = [];
$_SESSION['last_order_id'] = $order_id;

header("Location: order_success.php");
exit();
?>