<?php
include '../includes/auth_check.php';
include '../includes/db.php';

$message = '';
$error = '';

$cat_sql = "SELECT * FROM categories ORDER BY category_name ASC";
$cat_result = mysqli_query($conn, $cat_sql);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $dietary_type = trim($_POST['dietary_type']);
    $description = trim($_POST['description']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Image Upload Logic
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $image_name;
        
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    if (!empty($name) && $category_id > 0 && $price > 0) {
        $sql = "INSERT INTO menu_items (name, category_id, price, dietary_type, description, image, is_available) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sidsssi", $name, $category_id, $price, $dietary_type, $description, $image_name, $is_available);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: menu_management.php?msg=added");
            exit();
        } else {
            $error = "Failed to add dish: " . mysqli_error($conn);
        }
    } else {
        $error = "Please fill in all required fields properly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Dish - Sun & Sea Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0b1325;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Header Style */
        .admin-header {
            background: #060c18;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 136, 0.2);
            box-shadow: 0 4px 20px rgba(0, 255, 136, 0.05);
        }

        .admin-header .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .admin-nav {
            display: flex;
            gap: 12px;
        }

        .admin-nav .nav-btn {
            text-decoration: none;
            color: #00ff88;
            padding: 8px 18px;
            border: 1.5px solid #00ff88;
            border-radius: 25px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.15);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-nav .nav-btn:hover {
            background: #00ff88;
            color: #0b1325;
            box-shadow: 0 0 15px #00ff88;
            transform: translateY(-2px);
        }

        /* Form Wrapper Container */
        .form-container {
            max-width: 650px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .form-card {
            background: rgba(15, 25, 48, 0.85);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 12px;
            padding: 30px 35px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4), 0 0 10px rgba(0, 255, 136, 0.1);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 15px;
        }

        .form-header h2 {
            font-size: 22px;
            color: #00ff88;
            font-weight: 600;
        }

        .btn-back {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-back:hover {
            color: #ffffff;
        }

        /* Form Elements Style */
        .form-group {
            margin-bottom: 18px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 6px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px 14px;
            background: #060c18;
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #00ff88;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.3);
        }

        select option {
            background: #060c18;
            color: #ffffff;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Checkbox & File Upload Custom */
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #e2e8f0;
            font-size: 14px;
        }

        .checkbox-label input[type="checkbox"] {
            accent-color: #00ff88;
            width: 16px;
            height: 16px;
        }

        /* Charm Submit Button */
        .btn-submit {
            width: 100%;
            background: #00ff88;
            color: #0b1325;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 12px rgba(0, 255, 136, 0.3);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #33ff99;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.6);
            transform: translateY(-1px);
        }

        .error-msg {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid #ff4757;
            color: #ff4757;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="admin-header">
        <div class="logo-text">Sun & Sea Restaurant - Admin</div>
        <nav class="admin-nav">
            <a href="menu_management.php" class="nav-link nav-btn"><i class="fa-solid fa-utensils"></i> Menu</a>
            <a href="orders.php" class="nav-link nav-btn"><i class="fa-solid fa-list-check"></i> Orders</a>
        </nav>
    </header>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fa-solid fa-plus-circle"></i> Add New Dish</h2>
                <a href="menu_management.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Menu</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="add_item.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Dish Name *</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Seafood Fried Rice">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = mysqli_fetch_assoc($cat_result)) { ?>
                                <option value="<?php echo $cat['category_id']; ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (Rs.) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dietary_type">Dietary Type</label>
                        <select id="dietary_type" name="dietary_type">
                            <option value="Non-Veg">Non-Veg</option>
                            <option value="Veg">Veg</option>
                            <option value="Vegan">Vegan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image">Dish Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Brief details about ingredients or taste..."></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_available" value="1" checked>
                        Available for ordering immediately
                    </label>
                </div>

                <button type="submit" class="btn-submit"><i class="fa-solid fa-plus"></i> Save Dish</button>
            </form>
        </div>
    </div>

</body>
</html>