# Sun & Sea Restaurant - Restaurant Management & Ordering System

## Setup
1. Import `database/schema.sql` into MySQL (creates DB + tables + sample menu data).
2. Set DB credentials in `includes/db.php` if different from defaults.
3. Visit `admin/setup_admin.php` once in the browser to create the admin login (admin / admin123), then delete that file.
4. Visit `index.php` to view the customer site, or `admin/login.php` for the admin panel.

## Team Split (4 members)
- Member A: database/schema.sql, includes/db.php, includes/auth_check.php, admin/login.php, admin/logout.php, admin/setup_admin.php
- Member B: admin/dashboard.php, admin/menu_management.php, admin/add_item.php, admin/edit_item.php, admin/delete_item.php, assets/css/admin.css
- Member C: customer/index.php, customer/get_menu.php, includes/header.php, includes/footer.php, assets/js/menu.js, assets/css/style.css
- Member D: customer/cart.php, customer/add_to_cart.php, customer/checkout.php, customer/place_order.php, customer/order_success.php, admin/orders.php, assets/js/cart.js
