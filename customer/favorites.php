<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$fav_ids = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : [];
$fav_items = [];

if (!empty($fav_ids)) {
    $ids = implode(',', array_map('intval', $fav_ids));
    $query = "SELECT m.*, c.category_name 
              FROM menu_items m 
              LEFT JOIN categories c ON m.category_id = c.category_id 
              WHERE m.item_id IN ($ids)";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $fav_items[] = $row;
    }
}

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Sun & Sea Restaurant</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="site-header">
        <div class="logo">
            <a href="index.php">Sun & Sea Restaurant</a>
        </div>
        <nav>
            <a href="index.php" class="nav-link">Menu</a>
            <a href="favorites.php" class="nav-link">Favorites ❤️</a>
            <a href="cart.php" class="nav-link cart-btn">Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)</a>
        </nav>
    </header>

    <div style="padding: 30px 40px; max-width: 1200px; margin: 0 auto;">
        <h2>Your Favorite Dishes ❤️</h2>

        <?php if (empty($fav_items)) : ?>
            <p style="margin-top: 15px; font-size: 16px;">No favorite items added yet. <a href="index.php" style="color:#0b3d59; font-weight:bold;">Browse Menu</a></p>
        <?php else : ?>
            <div class="menu-grid" style="padding: 20px 0;">
                <?php foreach ($fav_items as $item) : ?>
                    <div class="menu-card" id="fav-card-<?php echo $item['item_id']; ?>">
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="menu-card-img" 
                             onerror="this.src='../assets/images/default.jpg';">
                        
                        <div class="card-header-flex">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <i class="fa-solid fa-heart fav-icon active-fav" onclick="removeFavorite(<?php echo $item['item_id']; ?>)"></i>
                        </div>

                        <p class="desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        <p class="tag"><?php echo htmlspecialchars($item['dietary_type'] . ' · ' . $item['category_name']); ?></p>
                        <p class="price">Rs. <?php echo number_format($item['price'], 2); ?></p>

                        <div class="cart-action-container">
                            <div class="qty-selector">
                                <button onclick="changeQty(<?php echo $item['item_id']; ?>, -1)">-</button>
                                <span id="qty-<?php echo $item['item_id']; ?>">1</span>
                                <button onclick="changeQty(<?php echo $item['item_id']; ?>, 1)">+</button>
                            </div>
                            <button class="add-cart-btn" onclick="addToCartWithQty(<?php echo $item['item_id']; ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function changeQty(itemId, change) {
        let qtyElement = document.getElementById(`qty-${itemId}`);
        let currentQty = parseInt(qtyElement.innerText);
        let newQty = currentQty + change;
        if (newQty >= 1) { qtyElement.innerText = newQty; }
    }

    function removeFavorite(itemId) {
        fetch('toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, action: 'remove' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                let card = document.getElementById(`fav-card-${itemId}`);
                if(card) { card.remove(); }
            }
        });
    }

    function addToCartWithQty(itemId) {
        let qty = parseInt(document.getElementById(`qty-${itemId}`).innerText);
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('cart-count').innerText = data.cart_count;
                alert('Item added to cart!');
            }
        });
    }
    </script>
</body>
</html>