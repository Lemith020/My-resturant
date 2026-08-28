<?php
include '../includes/auth_check.php';
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // is_available = 0 
    $stmt = mysqli_prepare($conn, "UPDATE menu_items SET is_available = 0 WHERE item_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: menu_management.php");
exit();
?>