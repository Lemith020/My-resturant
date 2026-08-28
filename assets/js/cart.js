function addToCart(itemId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `item_id=${itemId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
       
            document.getElementById('cart-count').innerText = data.cart_count;
            
            
            renderCartUI(data.cart_items);
        }
    });
}


function renderCartUI(cartItems) {
    const cartContainer = document.getElementById('cart-items-display'); 
    if (!cartContainer) return;

    cartContainer.innerHTML = '';

    cartItems.forEach(item => {
        cartContainer.innerHTML += `
            <div class="cart-item">
                <img src="../assets/images/${item.image}" alt="${item.name}" width="50">
                <div>
                    <h4>${item.name}</h4>
                    <p>Rs. ${item.price} x ${item.quantity}</p>
                </div>
            </div>
        `;
    });
}