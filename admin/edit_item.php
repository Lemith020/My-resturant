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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dish - Sun & Sea</title>
    <link rel="stylesheet" href="../assets/css/edit_item.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-text">Sun & Sea Restaurant - Admin</div>
        <nav class="admin-nav">
            <a href="menu_management.php" class="nav-link nav-btn"><i class="fa-solid fa-arrow-left"></i> Back to Menu</a>
            <a href="logout.php" class="nav-link nav-btn logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </header>

    <div class="dashboard-content">
        <div class="top-bar">
            <h1><i class="fa-solid fa-pen-to-square" style="color:#00ff88;"></i> Edit Dish</h1>
        </div>

        <div class="form-card">
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>NAME</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>DESCRIPTION</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>CATEGORY</label>
                    <select name="category_id" class="form-control">
                        <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $item['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>DIETARY TYPE</label>
                    <select name="dietary_type" class="form-control">
                        <option value="Vegetarian" <?php echo ($item['dietary_type']=='Vegetarian')?'selected':''; ?>>Vegetarian</option>
                        <option value="Non-Vegetarian" <?php echo ($item['dietary_type']=='Non-Vegetarian')?'selected':''; ?>>Non-Vegetarian</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>PRICE (RS.)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $item['price']; ?>" required>
                </div>

                <div class="form-group">
                    <label>IMAGE FILENAME</label>
                    <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($item['image_url']); ?>">
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_available" name="is_available" <?php echo $item['is_available'] ? 'checked' : ''; ?>>
                    <label for="is_available" style="margin-bottom: 0; color: #ffffff; cursor: pointer;">Available for Order</label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Update Dish
                </button>
            </form>
        </div>
    </div>
</body>
</html>