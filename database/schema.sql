-- Sun & Sea Restaurant Database Schema
CREATE DATABASE IF NOT EXISTS sun_sea_restaurant;
USE sun_sea_restaurant;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu Items Table
CREATE TABLE IF NOT EXISTS menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    dietary_type ENUM('Vegetarian','Non-Vegetarian') NOT NULL DEFAULT 'Non-Vegetarian',
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) DEFAULT 'default_dish.svg',
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_type ENUM('Pickup','Dine-in') NOT NULL,
    table_number VARCHAR(20) DEFAULT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_contact VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Card',
    payment_status ENUM('Pending','Paid') DEFAULT 'Pending',
    order_status ENUM('Pending','Preparing','Ready','Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Items (Breakdown per order)
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Seed Categories
INSERT INTO categories (category_id, category_name) VALUES
(1, 'Starters'),
(2, 'Mains'),
(3, 'Desserts'),
(4, 'Beverages')
ON DUPLICATE KEY UPDATE category_name=VALUES(category_name);

-- Seed Default Admin: admin / admin123
INSERT INTO admins (admin_id, username, password) VALUES
(1, 'admin', '$2y$10$.wKAFEpO1OFpX.CSqEmy0O0hGCbNzsVW011NHyEkKgKv2ShgWiZ1O')
ON DUPLICATE KEY UPDATE password=VALUES(password);

-- Seed Sample Menu Items
INSERT INTO menu_items (item_id, category_id, name, description, dietary_type, price, image_url, is_available) VALUES
(1, 1, 'Prawn Cocktail', 'Succulent fresh coastal prawns served with a creamy zesty cocktail sauce and lemon wedge.', 'Non-Vegetarian', 950.00, 'prawn_cocktail.svg', 1),
(2, 1, 'Crispy Garlic Bread', 'Toasted artisan baguette slices infused with aromatic roasted garlic butter and fresh herbs.', 'Vegetarian', 450.00, 'garlic_bread.svg', 1),
(3, 2, 'Grilled Seer Fish Steak', 'Locally caught fresh seer fish steak marinated in island spices, grilled to perfection with lemon butter sauce.', 'Non-Vegetarian', 2200.00, 'seer_fish.svg', 1),
(4, 2, 'Vegetable Fried Rice', 'Aromatic wok-tossed basmati rice with farm-fresh garden vegetables, cashew nuts, and sesame glaze.', 'Vegetarian', 850.00, 'veg_fried_rice.svg', 1),
(5, 2, 'Devilled Calamari', 'Tender calamari rings wok-fried with sweet peppers, onions, and spicy Sri Lankan chilli sauce.', 'Non-Vegetarian', 1850.00, 'calamari.svg', 1),
(6, 3, 'Authentic Watalappan', 'Traditional Sri Lankan spiced jaggery custard steamed with coconut cream, cardamom, and roasted cashews.', 'Vegetarian', 400.00, 'watalappan.svg', 1),
(7, 4, 'Fresh Coastal Lime Juice', 'Chilled refreshing lime cooler pressed fresh with mint sprigs and pure organic sugar syrup.', 'Vegetarian', 300.00, 'lime_juice.svg', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), price=VALUES(price), image_url=VALUES(image_url), dietary_type=VALUES(dietary_type);
