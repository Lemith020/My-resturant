<?php
session_start();
include '../includes/db.php';

$categories = mysqli_query($conn, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sun & Sea Restaurant - Menu</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <section class="hero">
        <h1>Sun & Sea Restaurant</h1>
        <p>Fresh flavours by the coast</p>
    </section>

    <section class="filter-bar">
        <input type="text" id="search-box" placeholder="Search dishes...">
        <select id="category-filter">
            <option value="0">All Categories</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
            <?php } ?>
        </select>
        <select id="dietary-filter">
            <option value="">All Types</option>
            <option value="Vegetarian">Vegetarian</option>
            <option value="Non-Vegetarian">Non-Vegetarian</option>
        </select>
    </section>

    <section id="menu-grid" class="menu-grid"></section>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/menu.js"></script>
    <script src="../assets/js/cart.js"></script>
</body>
</html>
