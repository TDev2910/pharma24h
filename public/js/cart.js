/**
 * Cart functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart.js loaded');
    
    // Cập nhật số lượng ban đầu với error handling
    try {
        updateCartCount();
    } catch (error) {
        console.warn('Error updating cart count:', error);
    }
    
    // Load giỏ hàng khi click vào icon (Event Delegation)
    document.addEventListener('show.bs.dropdown', function(e) {
        if (e.target.closest('.cart-dropdown')) {
            loadCartItems();
        }
    });
   
    //tạo handlers cho cart items
    initCartItemHandlers();
    
    // Thêm event delegation cho các nút có thể được thêm sau khi trang đã tải
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart')) {
            e.preventDefault();
            const button = e.target.closest('.add-to-cart');
            console.log('Add to cart clicked via delegation');
            
            const itemId = button.dataset.itemId;
            const itemType = button.dataset.itemType;
            const quantity = 1;
            
            console.log('Item data (delegation):', itemId, itemType, quantity);
            addToCart(itemId, itemType, quantity);
        }
    });
});

/**
 * Thêm sản phẩm vào giỏ hàng
 */
function addToCart(itemId, itemType, quantity = 1) {
    console.log('Adding to cart:', itemId, itemType, quantity);
    
    // CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    // Tạo form data
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('item_type', itemType);
    formData.append('quantity', quantity);
    formData.append('_token', token);
    
    // Hiển thị loader hoặc disabled button nếu cần
    
    // Gửi request
    fetch('/cart/add', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Cart response status:', response.status);
        // Kiểm tra content-type để đảm bảo đó là JSON
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            // Nếu không phải JSON, throw error để xử lý ở catch
            throw new Error("Response is not JSON: " + contentType);
        }
    })
    .then(data => {
        console.log('Cart response data:', data);
        if (data.success) {
            // Cập nhật số lượng trong giỏ
            updateCartCountDisplay(data.cart_count);
            
            // Hiển thị thông báo thành công
            showNotification('Đã thêm vào giỏ hàng!', 'success');
            
            // Tự động mở dropdown giỏ hàng
            const desktopCartDropdown = document.querySelector('#cartDropdown');
            if (desktopCartDropdown) {
                const bsDropdown = new bootstrap.Dropdown(desktopCartDropdown);
                bsDropdown.show();
                // Load lại items cho tất cả các giỏ hàng
                loadCartItems();
            }
        } else {
            // Hiển thị thông báo lỗi
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng. Vui lòng thử lại sau.', 'error');
    });
}

/**
 * Cập nhật hiển thị số lượng trong giỏ
 */
function updateCartCountDisplay(count) {
    const countElements = document.querySelectorAll('.cart-count');
    countElements.forEach(element => {
        element.textContent = count;
        
        // Ẩn badge khi count = 0
        if (count > 0) {
            element.style.display = 'block';
            // Hiệu ứng nhấp nháy
            element.classList.add('highlight');
            setTimeout(() => {
                element.classList.remove('highlight');
            }, 1000);
        } else {
            element.style.display = 'none';
        }
    });
}

/**
 * Lấy và hiển thị số lượng sản phẩm trong giỏ ở trang home chính
 */
function updateCartCount() {
    fetch('/cart?format=json', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Cart count response status:', response.status);
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            throw new Error("Response is not JSON: " + contentType);
        }
    })
    .then(data => {
        console.log('Cart count data:', data);
        updateCartCountDisplay(data.count);
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
        // Không hiển thị thông báo lỗi cho hàm này để tránh làm phiền người dùng
    });
}

/**
 * Load danh sách sản phẩm trong giỏ hàng
 */
function loadCartItems() {
    // Hiển thị trạng thái loading
    const loadingElements = document.querySelectorAll('.cart-loading');
    const cartItemsElements = document.querySelectorAll('.cart-items');
    const cartFooterElements = document.querySelectorAll('.cart-footer');
    
    loadingElements.forEach(el => el.classList.remove('d-none'));
    cartItemsElements.forEach(el => el.classList.add('d-none'));
    cartFooterElements.forEach(el => el.classList.add('d-none'));
    
    fetch('/cart?format=json', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Load cart items response status:', response.status);
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            throw new Error("Response is not JSON: " + contentType);
        }
    })
    .then(data => {
        console.log('Cart items data:', data);
        // Ẩn loading, hiển thị nội dung
        loadingElements.forEach(el => el.classList.add('d-none'));
        cartItemsElements.forEach(el => el.classList.remove('d-none'));
        cartFooterElements.forEach(el => el.classList.remove('d-none'));
        
        // Render items và cập nhật tổng tiền
        renderCartItems(data.preview_items);
        
        const totalElements = document.querySelectorAll('.cart-total-amount');
        totalElements.forEach(el => {
            const formattedTotal = formatPrice(data.total) + ' VNĐ';
            el.textContent = formattedTotal;
            // Cập nhật aria-label cho screen reader
            el.setAttribute('aria-label', 'Tổng tiền giỏ hàng: ' + formattedTotal);
        });
        
        // Khởi tạo lại handlers
        initCartItemHandlers();
    })
    .catch(error => {
        console.error('Error loading cart items:', error);
        
        // Ẩn loading, hiển thị thông báo lỗi
        loadingElements.forEach(el => el.classList.add('d-none'));
        
        cartItemsElements.forEach(el => {
            el.classList.remove('d-none');
            el.innerHTML = '<div class="text-center text-danger py-3">Không thể tải giỏ hàng. Vui lòng thử lại.</div>';
        });
        
        cartFooterElements.forEach(el => el.classList.remove('d-none'));
    });
}

/**
 * Render danh sách sản phẩm trong giỏ hàng
 */
function renderCartItems(items) {
    const cartItemsContainers = document.querySelectorAll('.cart-items');
    if (cartItemsContainers.length === 0) return;
    
    if (!items || items.length === 0) {
        cartItemsContainers.forEach(container => {
            container.innerHTML = '<div class="empty-cart text-center py-3"><p class="mb-0">Giỏ hàng trống</p></div>';
        });
        return;
    }
    
    let html = '';
    
    items.forEach(item => {
        const imageUrl = item.image 
            ? '/storage/' + item.image 
            : '/images/products/default.jpg';
            
        html += `
            <div class="cart-item p-2" data-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${imageUrl}" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">${formatPrice(item.price)} VNĐ</div>
                    <div class="cart-item-quantity">
                        <button class="btn-qty-decrease">-</button>
                        <input type="number" value="${item.quantity}" min="1" max="99" class="form-control form-control-sm item-quantity">
                        <button class="btn-qty-increase">+</button>
                    </div>
                </div>
                <button class="cart-item-remove" title="Xóa">×</button>
            </div>
        `;
    });
    
    cartItemsContainers.forEach(container => {
        container.textContent = ''; // Clear content safely
        container.innerHTML = html;
    });
}

/**
 * Khởi tạo event handlers cho các nút trong cart item
 */
function initCartItemHandlers() {
    // Xử lý nút tăng số lượng
    document.querySelectorAll('.btn-qty-increase').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const cartId = cartItem.dataset.id;
            const quantityInput = cartItem.querySelector('.item-quantity');
            const quantity = parseInt(quantityInput.value) + 1;
            
            updateItemQuantity(cartId, quantity);
        });
    });
    
    // Xử lý nút giảm số lượng
    document.querySelectorAll('.btn-qty-decrease').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const cartId = cartItem.dataset.id;
            const quantityInput = cartItem.querySelector('.item-quantity');
            const quantity = parseInt(quantityInput.value) - 1;
            
            if (quantity >= 1) {
                updateItemQuantity(cartId, quantity);
            }
        });
    });
    
    // Xử lý input số lượng
    document.querySelectorAll('.item-quantity').forEach(input => {
        input.addEventListener('change', function() {
            const cartItem = this.closest('.cart-item');
            const cartId = cartItem.dataset.id;
            let quantity = parseInt(this.value);
            
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            updateItemQuantity(cartId, quantity);
        });
    });
    
    // Xử lý nút xóa
    document.querySelectorAll('.cart-item-remove').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const cartId = cartItem.dataset.id;
            
            removeCartItem(cartId);
        });
    });
}

/**
 * Cập nhật số lượng sản phẩm
 */
function updateItemQuantity(cartId, quantity) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    formData.append('_token', token);
    
    fetch('/cart/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCountDisplay(data.cart?.count || 0);
            renderCartItems(data.cart?.preview_items || []);
            
            const totalElements = document.querySelectorAll('.cart-total-amount');
            totalElements.forEach(el => {
                el.textContent = formatPrice(data.cart?.total || 0) + ' VNĐ';
            });
            
            // Khởi tạo lại handlers
            initCartItemHandlers();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => console.error('Error updating cart:', error));
}

/**
 * Xóa sản phẩm khỏi giỏ hàng
 */
function removeCartItem(cartId) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!token) {
        console.error('CSRF token not found');
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('_token', token);
    
    fetch('/cart/remove', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCountDisplay(data.cart?.count || 0);
            renderCartItems(data.cart?.preview_items || []);
            
            const totalElements = document.querySelectorAll('.cart-total-amount');
            totalElements.forEach(el => {
                el.textContent = formatPrice(data.cart?.total || 0) + ' VNĐ';
            });
            
            // Khởi tạo lại handlers
            initCartItemHandlers();
            
            // Thông báo
            showNotification('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => console.error('Error removing from cart:', error));
}

/**
 * Định dạng giá tiền
 */
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

/**
 * Hiển thị thông báo
 */
function showNotification(message, type = 'success') {
    // Nếu bạn có thư viện toast notification như Toastify, SweetAlert2, ...
    // Bạn có thể sử dụng nó ở đây
    
    // Hoặc hiện thị toast bootstrap nếu có
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(toastEl);
        
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });
        toast.show();
        
        // Clean up after hiding
        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    } else {
        // Fallback đơn giản
        alert(message);
    }
}

window.addToCart = addToCart;
window.updateCartCount = updateCartCount;
window.loadCartItems = loadCartItems;
window.showNotification = showNotification;