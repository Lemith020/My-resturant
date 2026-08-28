<?php
session_start();
header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);
$item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;

if ($item_id > 0 && isset($_SESSION['cart'][$item_id])) {
  
    unset($_SESSION['cart'][$item_id]);

   
    $total_count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $val) {
            $total_count += is_array($val) ? ($val['quantity'] ?? 1) : (int)$val;
        }
    }

    echo json_encode([
        'status' => 'success',
        'cart_count' => $total_count
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Item not found in cart'
    ]);
}
?>