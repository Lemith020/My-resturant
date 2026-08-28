function loadMenu() {
    const search = document.getElementById('search-box').value;
    const category = document.getElementById('category-filter').value;
    const dietary = document.getElementById('dietary-filter').value;

    const url = `get_menu.php?search=${encodeURIComponent(search)}&category=${category}&dietary=${dietary}`;

    fetch(url)
        .then(res => res.json())
        .then(items => {
            const grid = document.getElementById('menu-grid');
            grid.innerHTML = '';

            if (items.length === 0) {
                grid.innerHTML = '<p>No dishes found.</p>';
                return;
            }
        

            items.forEach(item => {
                const card = document.createElement('div');
                card.className = 'menu-card';
        let isFav = item.is_favorite || false; 
        let iconClass = isFav ? 'fa-solid fa-heart fav-icon active-fav' : 'fa-regular fa-heart fav-icon';
                card.innerHTML = `
                    <img src="../assets/images/${item.image_url}" 
         alt="${item.name}" 
         class="menu-card-img" 
         onerror="this.src='../assets/images/default.jpg';">
    
    <div class="card-header-flex">
        <h3>${item.name}</h3>
        <i class="${iconClass}" onclick="toggleFavorite(${item.item_id}, this)"></i>
    </div>

    <p class="desc">${item.description}</p>
    <p class="tag">${item.dietary_type} · ${item.category_name}</p>
    <p class="price">Rs. ${parseFloat(item.price).toFixed(2)}</p>
    
    <div class="cart-action-container">
        <div class="qty-selector">
            <button onclick="changeQty(${item.item_id}, -1)">-</button>
            <span id="qty-${item.item_id}">1</span>
            <button onclick="changeQty(${item.item_id}, 1)">+</button>
        </div>
        <button class="add-cart-btn" onclick="addToCartWithQty(${item.item_id})">Add to Cart</button>
    </div>
                `;
                grid.appendChild(card);
            });
        });
}

document.getElementById('search-box').addEventListener('input', loadMenu);
document.getElementById('category-filter').addEventListener('change', loadMenu);
document.getElementById('dietary-filter').addEventListener('change', loadMenu);

loadMenu();

function changeQty(itemId, change) {
    let qtyElement = document.getElementById(`qty-${itemId}`);
    let currentQty = parseInt(qtyElement.innerText);
    let newQty = currentQty + change;
    if (newQty >= 1) {
        qtyElement.innerText = newQty;
    }
}

function addToCartWithQty(itemId) {
    let qtyElement = document.getElementById(`qty-${itemId}`);
   
    let selectedQty = qtyElement ? parseInt(qtyElement.innerText) : 1;

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ item_id: parseInt(itemId), quantity: selectedQty })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
           
            let cartCountElem = document.getElementById('cart-count');
            if(cartCountElem) {
                cartCountElem.innerText = data.cart_count;
            }
            alert(`Added ${selectedQty} item(s) to cart!`);
        } else {
            alert('Failed to add to cart');
        }
    })
    .catch(err => console.error('Error:', err));
}

function toggleFavorite(itemId, icon) {
    if (icon.classList.contains('fa-regular')) {
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid', 'active-fav');
        saveFavorite(itemId, 'add');
    } else {
        icon.classList.remove('fa-solid', 'active-fav');
        icon.classList.add('fa-regular');
        saveFavorite(itemId, 'remove');
    }
}

function saveFavorite(itemId, action) {
    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ item_id: itemId, action: action })
    });
}