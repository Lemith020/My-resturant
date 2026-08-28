<?php
include '../includes/db.php';

$username = "admin";
$plain_password = "admin123";
$hashed = password_hash($plain_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $hashed);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin created. Username: admin | Password: admin123";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
