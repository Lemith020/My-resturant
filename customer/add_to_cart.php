<?php
session_start();
$_SESSION['cart'][$menu_id] = [
    'name' => $product['name'],
    'price' => $product['price'],
    'quantity' => $quantity
];
header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);

$item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if ($item_id > 0) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

   
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }

  
    $total_count = array_sum($_SESSION['cart']);

    echo json_encode([
        'status' => 'success',
        'cart_count' => $total_count
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid Item ID or Quantity'
    ]);
}
?>