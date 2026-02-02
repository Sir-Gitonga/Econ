@extends('layouts.admin')
@section('content')

<style>
    /* Modern POS Styling */
    .main-content-inner {
        background: #667eea;
        min-height: 100vh;
        padding: 20px;
    }

    .pos-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        animation: fadeInDown 0.6s ease;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pos-header h3 {
        font-size: 2.2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pos-header h3::before {
        content: '🏪';
        font-size: 2rem;
    }

    .customer-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .customer-section label {
        font-weight: 600;
        color: #555;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .customer-section label::before {
        content: '👤';
        font-size: 1.2rem;
    }

    /* Enhanced form controls */
    #customerType, #productSearch, #paymentMethod {
        padding: 12px 16px;
        border: 2px solid #e1e5e9;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    #customerType:focus, #productSearch:focus, #paymentMethod:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    /* Grid layout improvements */
    .grid {
        display: grid;
    }

    .grid-cols-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .col-span-2 {
        grid-column: span 2;
    }

    .col-span-1 {
        grid-column: span 1;
    }

    .gap-6 {
        gap: 30px;
    }

    .gap-4 {
        gap: 20px;
    }

    .grid-cols-4 {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }

    /* Products section */
    .products-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        height: calc(100vh - 200px);
        animation: fadeInLeft 0.6s ease;
    }

    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    #productSearch {
        width: 100%;
        margin-bottom: 20px;
        position: relative;
    }

    #productSearch::placeholder {
        color: #9ca3af;
    }

    #productList {
        max-height: calc(100vh - 320px);
        overflow-y: auto;
        padding-right: 10px;
    }

    /* Product cards */
    .product-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }

    .product-card:nth-child(1) { animation-delay: 0.1s; }
    .product-card:nth-child(2) { animation-delay: 0.2s; }
    .product-card:nth-child(3) { animation-delay: 0.3s; }
    .product-card:nth-child(4) { animation-delay: 0.4s; }
    .product-card:nth-child(5) { animation-delay: 0.5s; }
    .product-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s;
    }

    .product-card:hover::before {
        left: 100%;
    }

    .product-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
    }

    .product-card img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .product-card:hover img {
        transform: scale(1.1);
    }

    .product-card h5 {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .product-card p {
        font-weight: 700;
        color: #667eea;
        font-size: 16px;
        margin: 0;
    }

    .stock-info {
        font-size: 12px;
        color: #10b981;
        margin-top: 5px;
    }

    .stock-info.out-of-stock {
        color: #ef4444;
    }

    /* Cart section */
    .cart-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
        animation: fadeInRight 0.6s ease;
    }

    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .cart-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .cart-header::before {
        content: '🛒';
        font-size: 1.5rem;
    }

    .cart-header h4 {
        color: #333;
        font-size: 1.5rem;
        margin: 0;
    }

    #cartTable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    #cartTable th {
        background: #f8fafc;
        padding: 12px 8px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }

    #cartTable td {
        padding: 12px 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .cart-total-section {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 20px;
    }

    #cartTotal {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .payment-section {
        margin-bottom: 20px;
    }

    .payment-section label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #555;
        margin-bottom: 10px;
    }

    .payment-section label::before {
        content: '💳';
        font-size: 1.2rem;
    }

    /* Enhanced checkout button */
    #checkoutBtn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    #checkoutBtn::before {
        content: '✨';
        font-size: 1.2rem;
    }

    #checkoutBtn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    #checkoutBtn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Cart items styling */
    .cart-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        margin-bottom: 10px;
        background: #f8fafc;
        border-radius: 12px;
        transition: all 0.3s ease;
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .cart-item:hover {
        background: #e2e8f0;
        transform: translateX(5px);
    }

    .empty-cart {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .empty-cart::before {
        content: '🛒';
        font-size: 4rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    /* Notification Styles */
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 400px;
    }

    .notification {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transform: translateX(450px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 5px solid;
        position: relative;
        overflow: hidden;
    }

    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.success {
        border-left-color: #10b981;
    }

    .notification.error {
        border-left-color: #ff0000;
    }

    .notification.warning {
        border-left-color: #f59e0b;
    }

    .notification.info {
        border-left-color: #3b82f6;
    }

    .notification-icon {
        font-size: 24px;
        margin-right: 15px;
        display: inline-block;
    }

    .notification-content {
        display: flex;
        align-items: center;
    }

    .notification-text {
        flex-grow: 1;
    }

    .notification-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 5px;
    }

    .notification-message {
        color: #6b7280;
        font-size: 14px;
    }

    .notification-close {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #9ca3af;
        transition: color 0.2s;
    }

    .notification-close:hover {
        color: #374151;
    }

    /* Receipt Modal Styles */
    .receipt-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .receipt-modal.show {
        opacity: 1;
        visibility: visible;
    }

    .receipt-content {
        background: white;
        width: 400px;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 15px;
        position: relative;
        transform: scale(0.8);
        transition: transform 0.3s ease;
    }

    .receipt-modal.show .receipt-content {
        transform: scale(1);
    }

    .receipt-header {
        padding: 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        text-align: center;
        border-radius: 15px 15px 0 0;
    }

    .receipt-body {
        padding: 25px;
        font-family: 'Courier New', monospace;
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 5px 0;
        border-bottom: 1px dotted #ddd;
    }

    .receipt-total {
        font-weight: bold;
        font-size: 18px;
        border-top: 2px solid #333;
        margin-top: 15px;
        padding-top: 10px;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #ddd;
        color: #666;
        font-size: 12px;
    }

    .receipt-buttons {
        display: flex;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 0 0 15px 15px;
    }

    .receipt-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-print {
        background: #10b981;
        color: white;
    }

    .btn-print:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .btn-close {
        background: #6b7280;
        color: white;
    }

    .btn-close:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    /* Pulse animation for add to cart */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .pulse {
        animation: pulse 0.3s ease;
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #5a6fd8;
    }

    /* Responsive design */
    @media (max-width: 1024px) {
        .grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .col-span-2, .col-span-1 {
            grid-column: span 1;
        }

        .grid-cols-4 {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .notification-container {
            right: 10px;
            left: 10px;
            max-width: none;
        }

        .receipt-content {
            width: 95%;
            margin: 20px;
        }
    }

    @media print {
        .receipt-buttons,
        .receipt-modal {
            display: none !important;
        }

        .receipt-content {
            transform: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <!-- Header Section -->
        <div class="pos-header">
            <h3>POS - Walk-in Customer</h3>
            <div class="customer-section">
                <label>Customer</label>
                <select id="customerType" class="w-64">
                    <option value="walkin">Walk-in Customer</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">

            <!-- Products Section -->
            <div class="col-span-2 products-container">
                <input type="text" id="productSearch" placeholder="Search or scan barcode...">

                <div class="grid grid-cols-4 gap-4" id="productList">
                    @foreach($products as $product)
                        <div class="product-card add-to-cart"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->sale_price ?? $product->regular_price }}"
                            data-stock="{{ $product->quantity }}">
                            <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}"
                                 alt="{{ $product->name }}">
                            <h5>{{ $product->name }}</h5>
                            <p>Ksh {{ number_format($product->sale_price ?? $product->regular_price) }}</p>
                            <small class="stock-info {{ $product->quantity > 0 ? '' : 'out-of-stock' }}">
                                {{ $product->quantity > 0 ? "Stock: {$product->quantity}" : "Out of Stock" }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-span-1 cart-container">
                <div class="cart-header">
                    <h4>Cart</h4>
                </div>

                <div id="cartItems" class="flex-grow" style="flex-grow: 1; overflow-y: auto;">
                    <div class="empty-cart">
                        <div>Cart is empty</div>
                        <small>Add items to start shopping</small>
                    </div>
                </div>

                <div class="cart-total-section">
                    <div id="cartTotal">Ksh 0</div>
                    <div>Total Amount</div>
                </div>

                <!-- Payment method -->
                <div class="payment-section">
                    <label>Payment Method</label>
                    <select id="paymentMethod">
                        <option value="cash"> Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <button id="checkoutBtn" disabled>Complete Sale</button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div class="notification-container" id="notificationContainer"></div>

<!-- Receipt Modal -->
<div class="receipt-modal" id="receiptModal">
    <div class="receipt-content">
        <div class="receipt-header">
            <h2>🧾 Receipt</h2>
            <p>Thank you for your purchase!</p>
        </div>
        <div class="receipt-body" id="receiptBody">
            <!-- Receipt content will be populated here -->
        </div>
        <div class="receipt-buttons">
            <button class="receipt-btn btn-print" onclick="printReceipt()">🖨️ Print Receipt</button>
            <button class="receipt-btn btn-close" onclick="closeReceipt()">❌ Close</button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let total = 0;
    let receiptNumber = 1000;

    // Notification System
    class NotificationManager {
        constructor() {
            this.container = document.getElementById('notificationContainer');
        }

        show(type, title, message, duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };

            notification.innerHTML = `
                <button class="notification-close" onclick="this.parentElement.remove()">×</button>
                <div class="notification-content">
                    <span class="notification-icon">${icons[type]}</span>
                    <div class="notification-text">
                        <div class="notification-title">${title}</div>
                        <div class="notification-message">${message}</div>
                    </div>
                </div>
            `;

            this.container.appendChild(notification);

            // Show animation
            setTimeout(() => notification.classList.add('show'), 100);

            // Auto remove
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 400);
                }
            }, duration);

            // Play sound
            this.playSound(type);
        }

        playSound(type) {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();

            const frequencies = {
                success: [523, 659, 784], // C, E, G (major chord)
                error: [200, 150], // Low frequencies for error
                warning: [440, 554], // A, C# (warning tone)
                info: [523, 659] // C, E (gentle tone)
            };

            const freq = frequencies[type] || [440];

            freq.forEach((frequency, index) => {
                setTimeout(() => {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = frequency;
                    oscillator.type = type === 'success' ? 'sine' : 'square';

                    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                }, index * 100);
            });
        }
    }

    const notify = new NotificationManager();

    // Add to cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart')) {
            const productCard = e.target.closest('.add-to-cart');
            const productId = productCard.dataset.id;
            const productName = productCard.dataset.name;
            const productPrice = parseFloat(productCard.dataset.price);
            const productStock = parseInt(productCard.dataset.stock);

            // Check stock availability
            if (productStock <= 0) {
                notify.show('error', 'Out of Stock', `${productName} is currently out of stock!`);
                return;
            }

            // Add pulse animation
            productCard.classList.add('pulse');
            setTimeout(() => productCard.classList.remove('pulse'), 300);

            addToCart(productId, productName, productPrice, productStock);
        }
    });

    function addToCart(id, name, price, stock) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.quantity >= stock) {
                notify.show('warning', 'Stock Limit', `Only ${stock} items available for ${name}`);
                return;
            }
            existingItem.quantity += 1;
            existingItem.total = existingItem.quantity * existingItem.price;
            notify.show('info', 'Quantity Updated', `${name} quantity increased to ${existingItem.quantity}`);
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1,
                total: price,
                stock: stock
            });
            notify.show('success', 'Item Added', `${name} added to cart`);
        }

        updateCartDisplay();
    }

    function removeFromCart(id) {
        const item = cart.find(item => item.id === id);
        if (item) {
            notify.show('info', 'Item Removed', `${item.name} removed from cart`);
        }
        cart = cart.filter(item => item.id !== id);
        updateCartDisplay();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            const newQuantity = item.quantity + change;

            if (newQuantity <= 0) {
                removeFromCart(id);
                return;
            }

            if (newQuantity > item.stock) {
                notify.show('warning', 'Stock Limit', `Only ${item.stock} items available for ${item.name}`);
                return;
            }

            item.quantity = newQuantity;
            item.total = item.quantity * item.price;
            updateCartDisplay();
        }
    }

    function updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const checkoutBtn = document.getElementById('checkoutBtn');

        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="empty-cart">
                    <div>Cart is empty</div>
                    <small>Add items to start shopping</small>
                </div>
            `;
            total = 0;
            checkoutBtn.disabled = true;
        } else {
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div style="flex-grow: 1;">
                        <div style="font-weight: 600; color: #333; margin-bottom: 4px;">${item.name}</div>
                        <div style="color: #667eea; font-size: 14px;">Ksh ${item.price.toLocaleString()}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; margin: 0 15px;">
                        <button onclick="updateQuantity('${item.id}', -1)" style="width: 30px; height: 30px; border: none; border-radius: 50%; background: #667eea; color: white; cursor: pointer; font-size:25px;">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity('${item.id}', 1)" style="width: 30px; height: 30px; border: none; border-radius: 50%; background: #667eea; color: white; cursor: pointer; font-size:25px;">+</button>
                    </div>
                    <button onclick="removeFromCart('${item.id}')" style="background: #ef4444; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size:30px;">×</button>
                </div>
            `).join('');

            total = cart.reduce((sum, item) => sum + item.total, 0);
            checkoutBtn.disabled = false;
        }

        cartTotal.textContent = `Ksh ${total.toLocaleString()}`;
    }

    // Search functionality
    document.getElementById('productSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const products = document.querySelectorAll('.product-card');
        let visibleCount = 0;

        products.forEach(product => {
            const productName = product.dataset.name.toLowerCase();
            if (productName.includes(searchTerm)) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        if (visibleCount === 0 && searchTerm.length > 0) {
            notify.show('info', 'No Results', `No products found matching "${searchTerm}"`);
        }
    });

    // Receipt Generation
    function generateReceipt() {
        const now = new Date();
        const receiptData = {
            receiptNumber: `RCP-${receiptNumber++}`,
            date: now.toLocaleDateString(),
            time: now.toLocaleTimeString(),
            cashier: 'POS System',
            paymentMethod: document.getElementById('paymentMethod').value,
            items: [...cart],
            subtotal: total,
            tax: total * 1, // 16% VAT
            total: total * 1
        };

        const receiptBody = document.getElementById('receiptBody');
        const paymentMethodNames = {
            cash: ' Cash Payment',
            mpesa: ' M-Pesa Payment',
            card: ' Card Payment'
        };

        receiptBody.innerHTML = `
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #333;"> STORE NAME</h3>
                <p style="margin: 5px 0; color: #666;">123 Main Street, City</p>
                <p style="margin: 5px 0; color: #666;">Tel: +254-XXX-XXXXX</p>
            </div>

            <div style="margin: 20px 0; padding: 10px 0; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                <div class="receipt-row">
                    <span>Receipt #:</span>
                    <span>${receiptData.receiptNumber}</span>
                </div>
                <div class="receipt-row">
                    <span>Date:</span>
                    <span>${receiptData.date}</span>
                </div>
                <div class="receipt-row">
                    <span>Time:</span>
                    <span>${receiptData.time}</span>
                </div>
                <div class="receipt-row">
                    <span>Cashier:</span>
                    <span>${receiptData.cashier}</span>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <h4 style="margin-bottom: 15px; color: #333;">Items Purchased:</h4>
                ${receiptData.items.map(item => `
                    <div class="receipt-row">
                        <span>${item.name}</span>
                        <span></span>
                    </div>
                    <div class="receipt-row" style="font-size: 12px; color: #666;">
                        <span>${item.quantity} x Ksh ${item.price.toLocaleString()}</span>
                        <span>Ksh ${item.total.toLocaleString()}</span>
                    </div>
                `).join('')}
            </div>

            <div style="margin: 20px 0; padding: 15px 0; border-top: 2px solid #333;">
                <div class="receipt-row">
                    <span>Subtotal:</span>
                    <span>Ksh ${receiptData.subtotal.toLocaleString()}</span>
                </div>
                <div class="receipt-row">
                    <span>VAT (16%):</span>
                    <span>Ksh ${receiptData.tax.toLocaleString()}</span>
                </div>
                <div class="receipt-row receipt-total">
                    <span>TOTAL:</span>
                    <span>Ksh ${receiptData.total.toLocaleString()}</span>
                </div>
                <div class="receipt-row" style="margin-top: 10px;">
                    <span>Payment Method:</span>
                    <span>${paymentMethodNames[receiptData.paymentMethod]}</span>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Thank you for your business!</p>
                <p>Please keep this receipt for your records</p>
                <p style="margin-top: 15px;">Generated by POS System v1.0</p>
            </div>
        `;

        return receiptData;
    }

    function showReceipt(receiptData) {
        const modal = document.getElementById('receiptModal');
        modal.classList.add('show');
    }

    function closeReceipt() {
        const modal = document.getElementById('receiptModal');
        modal.classList.remove('show');
    }

    function printReceipt() {
        const buttons = document.querySelector('.receipt-buttons');
        const originalDisplay = buttons.style.display;
        buttons.style.display = 'none';

        window.print();

        buttons.style.display = originalDisplay;

        notify.show('success', 'Receipt Printed', 'Receipt sent to printer successfully!');
    }

    // Checkout functionality
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        if (cart.length === 0) return;

        const paymentMethod = document.getElementById('paymentMethod').value;
        const originalText = this.innerHTML;

        // Show processing state
        this.innerHTML = '⏳ Processing Sale...';
        this.disabled = true;

        setTimeout(() => {
            const receiptData = generateReceipt();

            notify.show('success', 'Sale Completed!',
                `Total: Ksh ${(total * 1).toLocaleString()} | Payment: ${paymentMethod.toUpperCase()}`);

            showReceipt(receiptData);

            const saleData = {
                items: cart,
                subtotal: total,
                tax: total * 1,
                total: total * 1,
                payment_method: paymentMethod,
                customer_type: document.getElementById('customerType').value,
                receipt_number: receiptData.receiptNumber,
                timestamp: new Date().toISOString()
            };

            console.log('Sale Data:', saleData);

            // Reset cart
            cart = [];
            updateCartDisplay();

            // Reset button
            this.innerHTML = originalText;
            this.disabled = false;

        }, 1500);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // ESC to close receipt
        if (e.key === 'Escape') {
            closeReceipt();
        }

        // Ctrl+P to print receipt when modal is open
        if (e.ctrlKey && e.key === 'p') {
            const modal = document.getElementById('receiptModal');
            if (modal.classList.contains('show')) {
                e.preventDefault();
                printReceipt();
            }
        }

        // F1 to focus search
        if (e.key === 'F1') {
            e.preventDefault();
            document.getElementById('productSearch').focus();
        }


        if (e.key === 'Enter' && cart.length > 0) {
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (!checkoutBtn.disabled) {
                checkoutBtn.click();
            }
        }
    });

    document.getElementById('receiptModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReceipt();
        }
    });

    // Auto-focus search on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('productSearch').focus();
        notify.show('info', 'Welcome!', 'POS System is ready for transactions');
    });

    // Barcode scanner simulation (Enter key after typing in search)
    document.getElementById('productSearch').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                // Try to find exact match first (for barcode scanning)
                const products = document.querySelectorAll('.product-card');
                let found = false;

                products.forEach(product => {
                    const productName = product.dataset.name.toLowerCase();
                    const productId = product.dataset.id;


                    if (productId === searchTerm || productName === searchTerm.toLowerCase()) {
                        product.click();
                        found = true;
                        this.value = '';
                        return;
                    }
                });

                if (!found) {
                    notify.show('warning', 'Product Not Found', `No product found with barcode/name: ${searchTerm}`);
                }
            }
        }
    });

    // Initialize
    updateCartDisplay();

    // Welcome sound on page load
    window.addEventListener('load', function() {
        setTimeout(() => {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            [523, 659, 784, 1047].forEach((freq, index) => {
                setTimeout(() => {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = freq;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(0.05, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.4);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.4);
                }, index * 150);
            });
        }, 1000);
    });
</script>

@endsection
