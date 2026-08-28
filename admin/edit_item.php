<?php
include '../includes/auth_check.php';
include '../includes/db.php';

$id = (int) $_GET['id'];
$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $dietary_type = $_POST['dietary_type'];
    $price = (float) $_POST['price'];
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $sql = "UPDATE menu_items SET category_id=?, name=?, description=?, dietary_type=?, price=?, image_url=?, is_available=? WHERE item_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssdsii", $category_id, $name, $description, $dietary_type, $price, $image_url, $is_available, $id);
    mysqli_stmt_execute($stmt);

    header("Location: menu_management.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM menu_items WHERE item_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Dish</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-nav">
        <h2>Sun & Sea Restaurant - Admin</h2>
        <div>
            <a href="menu_management.php">Back to Menu</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="dashboard-content">
        <h1>Edit Dish</h1>
        <form method="POST" class="admin-form">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>

            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>

            <label>Category</label>
            <select name="category_id">
                <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $item['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label>Dietary Type</label>
            <select name="dietary_type">
                <option value="Vegetarian" <?php echo ($item['dietary_type']=='Vegetarian')?'selected':''; ?>>Vegetarian</option>
                <option value="Non-Vegetarian" <?php echo ($item['dietary_type']=='Non-Vegetarian')?'selected':''; ?>>Non-Vegetarian</option>
            </select>

            <label>Price (Rs.)</label>
            <input type="number" step="0.01" name="price" value="<?php echo $item['price']; ?>" required>

            <label>Image filename (mock)</label>
            <input type="text" name="image_url" value="<?php echo htmlspecialchars($item['image_url']); ?>">

            <label><input type="checkbox" name="is_available" <?php echo $item['is_available'] ? 'checked' : ''; ?>> Available</label>

            <button type="submit">Update Dish</button>
        </form>
    </div>
</body>
</html>
