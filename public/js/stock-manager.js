/**
 * Live Stock Update System
 * 
 * Provides real-time stock status updates across the frontend
 * - Checks stock availability before adding to cart
 * - Updates product stock displays
 * - Disables out-of-stock buttons
 * - Shows notifications for stock changes
 */

class StockManager {
    /**
     * Initialize stock manager
     */
    static init() {
        // Check all product cards on page load
        this.updateAllProductCards();
        
        // Poll for stock updates every 30 seconds
        setInterval(() => this.updateAllProductCards(), 30000);
        
        // Attach event listeners to add-to-cart buttons
        this.attachAddToCartListeners();
    }

    /**
     * Get current stock for a product
     * @param {number} productId 
     * @returns {Promise<Object>}
     */
    static async getProductStock(productId) {
        try {
            const response = await fetch(`/api/inventory/product/${productId}/stock`, {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.warn(`Failed to fetch stock for product ${productId}`);
                return null;
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching stock:', error);
            return null;
        }
    }

    /**
     * Get stock for multiple products at once
     * @param {number[]} productIds 
     * @returns {Promise<Object>}
     */
    static async getMultipleProductsStock(productIds) {
        try {
            const response = await fetch('/api/inventory/products/stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ ids: productIds })
            });

            if (!response.ok) {
                console.warn('Failed to fetch multiple products stock');
                return {};
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching multiple stocks:', error);
            return {};
        }
    }

    /**
     * Get low stock products for admin alerts
     * @returns {Promise<Object>}
     */
    static async getLowStockProducts() {
        try {
            const response = await fetch('/api/inventory/low-stock', {
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.warn('Failed to fetch low stock products');
                return null;
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching low stock products:', error);
            return null;
        }
    }

    /**
     * Check if product can be deducted
     * @param {number} productId 
     * @param {number} quantity 
     * @returns {Promise<boolean>}
     */
    static async canDeductStock(productId, quantity) {
        const stock = await this.getProductStock(productId);
        return stock && stock.stock_quantity >= quantity && stock.can_sell;
    }

    /**
     * Update all product cards on page
     */
    static async updateAllProductCards() {
        // Find all product cards with data-product-id
        const productCards = document.querySelectorAll('[data-product-id]');
        
        if (productCards.length === 0) return;

        // Collect product IDs
        const productIds = Array.from(productCards).map(card => 
            parseInt(card.dataset.productId)
        );

        // Fetch bulk stock data
        const stockData = await this.getMultipleProductsStock(productIds);

        // Update each card
        productCards.forEach(card => {
            const productId = parseInt(card.dataset.productId);
            const stock = stockData[productId];

            if (stock) {
                this.updateProductCard(card, stock);
            }
        });
    }

    /**
     * Update individual product card UI
     * @param {HTMLElement} card 
     * @param {Object} stock 
     */
    static updateProductCard(card, stock) {
        // Update stock quantity display
        const stockDisplay = card.querySelector('.product-stock-qty');
        if (stockDisplay) {
            stockDisplay.textContent = `${stock.stock_quantity} in stock`;
        }

        // Update status badge
        const statusBadge = card.querySelector('.product-stock-badge');
        if (statusBadge) {
            statusBadge.className = 'badge product-stock-badge';
            
            if (stock.stock_status === 'instock') {
                statusBadge.classList.add('bg-success');
                statusBadge.textContent = '✓ In Stock';
            } else if (stock.stock_status === 'lowstock') {
                statusBadge.classList.add('bg-warning');
                statusBadge.textContent = '⚠ Low Stock';
            } else {
                statusBadge.classList.add('bg-danger');
                statusBadge.textContent = '✗ Out of Stock';
            }
        }

        // Update or disable add-to-cart button
        const addBtn = card.querySelector('.btn-add-to-cart, btn-add-cart, [data-action="add-to-cart"]');
        if (addBtn) {
            if (!stock.can_sell) {
                addBtn.disabled = true;
                addBtn.classList.add('disabled');
                addBtn.title = 'Out of stock';
                addBtn.innerHTML = '<i class="fas fa-ban"></i> Out of Stock';
            } else {
                addBtn.disabled = false;
                addBtn.classList.remove('disabled');
                addBtn.title = 'Add to cart';
                if (!addBtn.hasAttribute('data-original-text')) {
                    addBtn.setAttribute('data-original-text', addBtn.textContent);
                }
                addBtn.textContent = addBtn.getAttribute('data-original-text') || 'Add to Cart';
            }
        }
    }

    /**
     * Attach listeners to add-to-cart buttons
     */
    static attachAddToCartListeners() {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-action="add-to-cart"], .btn-add-to-cart, .btn-add-cart');
            
            if (!btn) return;

            e.preventDefault();

            const productId = btn.dataset.productId || btn.closest('[data-product-id]')?.dataset.productId;
            const qty = parseInt(btn.dataset.quantity || 1);

            if (!productId) {
                console.error('Product ID not found');
                return;
            }

            // Check stock before allowing add
            const canAdd = await this.canDeductStock(productId, qty);

            if (!canAdd) {
                this.showNotification(
                    'Out of Stock',
                    'This product is not available',
                    'warning'
                );
                return;
            }

            // If form exists, submit it normally
            const form = btn.closest('form');
            if (form) {
                form.submit();
            } else {
                // Otherwise redirect to add route
                window.location.href = `/cart/add?id=${productId}&quantity=${qty}`;
            }
        });
    }

    /**
     * Show notification
     * @param {string} title 
     * @param {string} message 
     * @param {string} type success|danger|warning|info
     */
    static showNotification(title, message, type = 'info') {
        // Try Bootstrap toast if available
        if (typeof bootstrap !== 'undefined') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'warning' ? 'warning' : type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <strong>${title}:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const container = document.querySelector('.notification-container') || 
                             document.querySelector('body');
            container.prepend(alertDiv);

            // Auto-dismiss after 5 seconds
            setTimeout(() => alertDiv.remove(), 5000);
        } else {
            // Fallback to alert
            alert(`${title}: ${message}`);
        }
    }

    /**
     * Get CSRF token
     * @returns {string}
     */
    static getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || 
               document.querySelector('input[name="_token"]')?.value || '';
    }

    /**
     * Get auth token (for API calls)
     * @returns {string}
     */
    static getAuthToken() {
        // Try to get from meta tag or localStorage
        return document.querySelector('meta[name="auth-token"]')?.content || 
               localStorage.getItem('auth_token') || '';
    }

    /**
     * Update cart item validation
     */
    static validateCartItem(productId, quantity) {
        return this.canDeductStock(productId, quantity);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => StockManager.init());
} else {
    StockManager.init();
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StockManager;
}
