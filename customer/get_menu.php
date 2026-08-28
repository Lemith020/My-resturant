<?php
session_start(); 
include '../includes/db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$dietary = isset($_GET['dietary']) ? mysqli_real_escape_string($conn, $_GET['dietary']) : '';


$favs = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : [];

$sql = "SELECT m.*, c.category_name FROM menu_items m
        LEFT JOIN categories c ON m.category_id = c.category_id
        WHERE m.is_available = 1";

if ($search !== '') {
    $sql .= " AND m.name LIKE '%$search%'";
}
if ($category > 0) {
    $sql .= " AND m.category_id = $category";
}
if ($dietary !== '') {
    $sql .= " AND m.dietary_type = '$dietary'";
}

$result = mysqli_query($conn, $sql);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    // අදාළ item_id එක Favorite List එකේ තිබේදැයි බලයි (true / false)
    $row['is_favorite'] = in_array((int)$row['item_id'], array_map('intval', $favs));
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
?>