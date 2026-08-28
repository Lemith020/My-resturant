<?php
session_start();
header('Content-Type: application/json');


$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['item_id']) && isset($data['action'])) {
    $item_id = (int)$data['item_id'];
    $action = $data['action'];


    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }

    if ($action === 'add') {
        if (!in_array($item_id, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $item_id;
        }
    } elseif ($action === 'remove') {
        $_SESSION['favorites'] = array_values(array_diff($_SESSION['favorites'], [$item_id]));
    }

    echo json_encode([
        'status' => 'success', 
        'action' => $action, 
        'fav_count' => count($_SESSION['favorites']),
        'favorites' => $_SESSION['favorites']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
}
?>