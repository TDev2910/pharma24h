<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - PCT Pharma</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Progress Steps */
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            z-index: 2;
            border: 2px solid #e9ecef;
            position: relative;
        }
        
        .step.active .step-circle {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .step-name {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .step.active .step-name {
            color: #007bff;
            font-weight: 600;
        }
        
        .step-line {
            position: absolute;
            top: 20px;
            left: 50%;
            right: -50%;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        
        .step:last-child .step-line {
            display: none;
        }
        
        /* Cart Items */
        .cart-card {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .cart-header {
            background: white;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
        }
        
        .cart-item {
            padding: 24px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background-color: #fafafa;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #ddd;
            margin-right: 16px;
            flex-shrink: 0;
        }
        
        .item-checkbox:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            margin-right: 16px;
            flex-shrink: 0;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .item-type {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .item-price {
            font-weight: 700;
            color: #007bff;
            font-size: 18px;
        }
        
        .item-controls {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 16px;
            margin-left: 20px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 50px;
            padding: 4px;
            border: 1px solid #e9ecef;
        }
        
        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
            color: #666;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .qty-btn:hover {
            background: #007bff;
            color: white;
            transform: translateY(-1px);
        }
        
        .qty-input {
            border: none;
            background: transparent;
            text-align: center;
            width: 40px;
            font-weight: 600;
            color: #333;
        }
        
        .qty-input:focus {
            outline: none;
        }
        
        .item-subtotal {
            text-align: right;
        }
        
        .subtotal-label {
            font-size: 12px;
            color: #999;
            margin-bottom: 4px;
        }
        
        .subtotal-amount {
            font-weight: 700;
            font-size: 16px;
            color: #333;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .remove-btn:hover {
            background: #fee;
            transform: scale(1.1);
        }
        
        /* Sidebar */
        .sidebar-card {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .sidebar-body {
            padding: 24px;
        }
        
        .coupon-input {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .coupon-input input {
            flex: 1;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
        }
        
        .coupon-input input:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .apply-btn {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .apply-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        .applied-coupon {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .price-row.total {
            font-weight: 700;
            font-size: 18px;
            color: #333;
            padding-top: 16px;
            border-top: 2px solid #f0f0f0;
            margin-top: 16px;
        }
        
        .total-amount {
            color: #dc3545 !important;
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 16px 24px;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.3);
        }
        
        .continue-shopping {
            background: none;
            border: 2px solid #007bff;
            color: #007bff;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .continue-shopping:hover {
            background: #007bff;
            color: white;
        }
        
        /* Empty cart */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-cart i {
            color: #ddd;
            margin-bottom: 24px;
        }
        
        .empty-cart h3 {
            color: #333;
            margin-bottom: 12px;
        }
        
        .empty-cart p {
            color: #999;
            margin-bottom: 24px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cart-container {
                padding: 16px;
            }
            
            .cart-item {
                padding: 16px;
            }
            
            .item-controls {
                flex-direction: row;
                margin-left: 0;
                margin-top: 16px;
                justify-content: space-between;
                align-items: center;
            }
            
            .item-subtotal {
                text-align: left;
            }
            
            .sidebar-card {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step active">
                <div class="step-circle">1</div>
                <div class="step-name">Giỏ hàng</div>
                <div class="step-line"></div>
            </div>
            <div class="step">
                <div class="step-circle">2</div>
                <div class="step-name">Địa chỉ</div>
                <div class="step-line"></div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-name">Thanh toán</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Cart Items -->
                <div class="cart-card">
                    <div class="cart-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><span id="itemCount">{{ number_format($cartData['count'] ?? 0) }}</span> sản phẩm đã chọn</h5>
                            <button class="btn text-danger" id="removeAll">
                                <i class="fas fa-trash me-1"></i> Xóa tất cả
                            </button>
                        </div>
                    </div>
                    
                    @forelse(($cartData['items'] ?? []) as $cart)
                    <div class="cart-item" data-cart-id="{{ $cart->id }}">
                        <div class="d-flex align-items-start">
                            <input type="checkbox" class="item-checkbox" checked>
                            <div class="item-image">
                                @php
                                    $raw = $cart->image ?? '';
                                    $isHttp = preg_match('/^https?:\/\//i', (string) $raw);
                                    if (!$raw) {
                                        $imgUrl = 'https://placehold.co/80x80';
                                    } elseif ($isHttp) {
                                        $imgUrl = $raw;
                                    } else {
                                        $normalized = ltrim($raw, '/');
                                        if (str_starts_with($normalized, 'storage/')) {
                                            $imgUrl = asset($normalized);
                                        } else {
                                            $imgUrl = asset('storage/' . $normalized);
                                        }
                                    }
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $cart->name }}">
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $cart->name }}</div>
                                <div class="item-type">{{ $cart->item_type === 'medicine' ? 'Thuốc' : 'Hàng hóa' }}</div>
                                <div class="item-price" data-price="{{ (int) $cart->price }}">{{ number_format($cart->price) }} VNĐ</div>
                            </div>
                            <div class="item-controls">
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="changeQuantity(this, -1)">−</button>
                                    <input type="number" value="{{ (int) $cart->quantity }}" min="1" max="99" class="qty-input">
                                    <button class="qty-btn" onclick="changeQuantity(this, 1)">+</button>
                                </div>
                                <div class="item-subtotal">
                                    <div class="subtotal-label">Thành tiền</div>
                                    <div class="subtotal-amount">{{ number_format($cart->price * $cart->quantity) }} VNĐ</div>
                                </div>
                                <button class="remove-btn" onclick="removeItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart fa-3x"></i>
                        <h3>Giỏ hàng trống</h3>
                        <p>Tiếp tục mua sắm để thêm sản phẩm.</p>
                    </div>
                    @endforelse
                    
                    <div class="cart-footer p-3">
                        <button class="continue-shopping">
                            <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Coupon Section -->
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h5 class="fw-bold mb-0">Mã giảm giá</h5>
                    </div>
                    <div class="sidebar-body">
                        <div class="coupon-input">
                            <input type="text" placeholder="Nhập mã giảm giá">
                            <button class="apply-btn">Áp dụng</button>
                        </div>
                    </div>
                </div>

                <!-- Price Details -->  
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h5 class="fw-bold mb-0">Chi tiết giá</h5>
                    </div>
                    <div class="sidebar-body">
                        <div class="price-row">
                            <span><span id="summaryCount">{{ number_format($cartData['count'] ?? 0) }}</span> sản phẩm</span>
                            <span id="summarySubtotal">{{ number_format($cartData['total'] ?? 0) }} VNĐ</span>
                        </div>
                        <div class="price-row">
                            <span class="text-success">Giảm giá</span>
                            <span class="text-success">-0 VNĐ</span>
                        </div>
                        <div class="price-row total">
                            <span>Tổng thanh toán</span>
                            <span class="total-amount" id="grandTotal">{{ number_format($cartData['total'] ?? 0) }} VNĐ</span>
                        </div>
                        <button class="checkout-btn">
                            Tiến hành đặt hàng <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function changeQuantity(btn, change) {
            const input = btn.parentElement.querySelector('.qty-input');
            let value = parseInt(input.value) + change;
            if (value < 1) value = 1;
            if (value > 99) value = 99;
            input.value = value;
            
            // Update subtotal
            const item = btn.closest('.cart-item');
            updateSubtotal(item);
            // Sync to backend
            syncQuantity(item);
        }
        
        function updateSubtotal(item) {
            const price = parseInt(item.querySelector('.item-price').getAttribute('data-price'));
            const quantity = parseInt(item.querySelector('.qty-input').value);
            const subtotal = price * quantity;
            
            item.querySelector('.subtotal-amount').textContent = 
                new Intl.NumberFormat('vi-VN').format(subtotal) + ' VNĐ';
            
            updateTotal();
        }
        
        function updateTotal() {
            const items = document.querySelectorAll('.cart-item');
            let total = 0;
            
            items.forEach(item => {
                const checkbox = item.querySelector('.item-checkbox');
                if (checkbox.checked) {
                    const subtotal = parseInt(item.querySelector('.subtotal-amount').textContent.replace(/[^0-9]/g, ''));
                    total += subtotal;
                }
            });
            
            const formatted = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
            document.querySelector('.total-amount').textContent = formatted;
            const summaryCount = Array.from(document.querySelectorAll('.cart-item .item-checkbox'))
                .filter(cb => cb.checked).length;
            document.getElementById('summarySubtotal').textContent = formatted;
            document.getElementById('grandTotal').textContent = formatted;
            document.getElementById('itemCount').textContent = summaryCount;
            document.getElementById('summaryCount').textContent = summaryCount;
        }
        
        function removeItem(btn) {
            const item = btn.closest('.cart-item');
            const cartId = item.getAttribute('data-cart-id');
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cart_id: parseInt(cartId) })
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        item.remove();
                        updateTotal();
                        updateItemCount();
                    }, 300);
                }
            });
        }
        
        function updateItemCount() {
            const count = document.querySelectorAll('.cart-item').length;
            document.querySelector('.cart-header h5').innerHTML = '<span id="itemCount">' + count + '</span> sản phẩm đã chọn';
            document.getElementById('summaryCount').textContent = count;
        }
        
        function syncQuantity(item) {
            const cartId = parseInt(item.getAttribute('data-cart-id'));
            const quantity = parseInt(item.querySelector('.qty-input').value);
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cart_id: cartId, quantity })
            }).then(r => r.json()).then(() => {
                // no-op; UI đã cập nhật loptimistically
            });
        }
        
        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Checkbox change listeners
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });
            
            // Quantity input listeners
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    const item = this.closest('.cart-item');
                    updateSubtotal(item);
                    syncQuantity(item);
                });
            });
            
            // Remove all button
            document.getElementById('removeAll').addEventListener('click', function() {
                if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm?')) {
                    const items = Array.from(document.querySelectorAll('.cart-item'));
                    const removeNext = () => {
                        const it = items.shift();
                        if (!it) { updateTotal(); updateItemCount(); return; }
                        const cartId = parseInt(it.getAttribute('data-cart-id'));
                        fetch('{{ route('cart.remove') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ cart_id: cartId })
                        }).then(() => {
                            it.style.opacity = '0';
                            it.style.transform = 'translateX(100%)';
                            setTimeout(() => { it.remove(); removeNext(); }, 200);
                        });
                    };
                    removeNext();
                }
            });
            
            // Khởi tạo tổng tiền đúng theo dữ liệu server
            updateTotal();
        });
    </script>
</body>
</html>