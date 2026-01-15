<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - PCT Pharma</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="cart-container" style="margin-top: 100px;">
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
                            <i class="fas fa-arrow-left me-2"></i> <a href="{{ route('home') }}" style="color: #0d0c22;">Tiếp tục mua sắm</a>
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
                        <a href="/checkout" class="checkout-btn">Thanh toán</a>
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
<style>
    /* --- BASE STYLES --- */
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        margin-top: 100px;
    }

    /* CARD STYLES */
    .cart-card, .sidebar-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .cart-header, .sidebar-header {
        background: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
    }

    .sidebar-body { padding: 24px; }
    .cart-footer { border-top: 1px solid #f0f0f0; }

    /* --- CART ITEM STYLING (Phần danh sách viên uống) --- */
    .cart-item {
        padding: 24px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }
    .cart-item:hover { background-color: #fafafa; }
    .cart-item:last-child { border-bottom: none; }

    /* Layout chính của 1 dòng sản phẩm: Dùng Flexbox căn giữa dọc */
    .cart-item .d-flex {
        align-items: center !important; /* QUAN TRỌNG: Căn giữa tất cả theo chiều dọc */
    }

    .item-checkbox {
        width: 20px; height: 20px;
        border-radius: 6px; border: 2px solid #dee2e6;
        margin-right: 16px; flex-shrink: 0;
        cursor: pointer;
    }

    .item-image {
        width: 80px; height: 80px;
        border-radius: 12px; overflow: hidden;
        margin-right: 20px; flex-shrink: 0;
        background-color: #f8f9fa;
        border: 1px solid #f0f0f0;
    }
    .item-image img {
        width: 100%; height: 100%; object-fit: cover;
    }

    .item-info {
        flex: 1; /* Chiếm phần dư còn lại */
        padding-right: 15px;
    }

    .item-name {
        font-weight: 600; color: #333;
        font-size: 16px; margin-bottom: 4px;
        line-height: 1.4;
    }

    .item-type {
        background: #e3f2fd; color: #1976d2;
        padding: 2px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        display: inline-block; margin-bottom: 6px;
    }

    .item-price {
        font-weight: 700; color: #0d0c22; font-size: 15px;
    }

    /* CONTROLS (Nút tăng giảm, Giá tổng, Xóa) */
    .item-controls {
        display: flex;
        flex-direction: column; /* Desktop: Xếp dọc */
        align-items: flex-end; /* Căn lề phải */
        gap: 12px;
        min-width: 120px; /* Đảm bảo đủ rộng để không bị nhảy chữ */
    }

    .quantity-control {
        display: flex; align-items: center;
        background: #fff; border-radius: 50px;
        padding: 2px; border: 1px solid #dee2e6;
    }

    .qty-btn {
        width: 28px; height: 28px;
        border: none; background: #f8f9fa;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-weight: 600; color: #666;
        transition: all 0.2s;
    }
    .qty-btn:hover { background: #e9ecef; color: #000; }

    .qty-input {
        border: none; background: transparent;
        text-align: center; width: 32px;
        font-weight: 600; color: #333; font-size: 14px;
        padding: 0;
    }
    .qty-input:focus { outline: none; }
    /* Ẩn mũi tên input number */
    .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .subtotal-amount { font-weight: 700; color: #333; font-size: 16px; }
    .subtotal-label { display: none; }

    .remove-btn {
        background: none; border: none; color: #dc3545;
        cursor: pointer; padding: 4px; font-size: 14px;
        opacity: 0.7; transition: opacity 0.2s;
    }
    .remove-btn:hover { opacity: 1; transform: scale(1.1); }

    /* SIDEBAR (Cột phải) */
    .coupon-input { display: flex; gap: 8px; }
    .coupon-input input {
        flex: 1; border: 1px solid #dee2e6;
        border-radius: 8px; padding: 10px 14px; font-size: 14px;
    }
    .apply-btn {
        background: #0d0c22; color: white; border: none;
        border-radius: 8px; padding: 0 16px; font-weight: 600; font-size: 14px;
    }

    .price-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #555; }
    .price-row.total {
        font-size: 18px; padding-top: 16px; margin-top: 16px;
        border-top: 1px dashed #dee2e6; color: #000;
    }
    .total-amount { color: #dc3545; font-weight: 800; }

    .checkout-btn {
        display: flex; align-items: center; justify-content: center;
        width: 100%; height: 48px; background: #0D0C22;
        color: #fff; border-radius: 12px; font-weight: 600;
        text-decoration: none; margin-top: 20px; transition: 0.2s;
    }
    .checkout-btn:hover { background: #333; transform: translateY(-2px); }

    .continue-shopping {
        background: white; border: 1px solid #dee2e6;
        color: #333; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;
    }
    .continue-shopping a { text-decoration: none; color: inherit; }
    .continue-shopping:hover { background: #f8f9fa; border-color: #bbb; }


    /* --- RESPONSIVE MOBILE & TABLET --- */
    @media (max-width: 767.98px) {
        .cart-container {
            margin-top: 60px;
            padding: 12px;
        }

        .cart-card, .sidebar-card { border-radius: 12px; }
        .cart-item { padding: 15px; }

        /* Ẩn checkbox mặc định, dùng absolute để đặt vị trí */
        .cart-item .d-flex {
            display: grid; /* Chuyển sang Grid Layout cho Mobile */
            grid-template-columns: 80px 1fr; /* Cột 1: Ảnh, Cột 2: Nội dung */
            grid-template-rows: auto auto;
            gap: 12px;
            position: relative;
            align-items: start !important;
        }

        .item-checkbox {
            position: absolute;
            top: -5px; left: -5px;
            z-index: 5;
            background: white;
            margin: 0;
        }

        .item-image {
            width: 80px; height: 80px;
            margin: 0; /* Xóa margin cũ */
            grid-row: 1 / 3; /* Ảnh chiếm 2 dòng */
        }

        .item-info {
            padding: 0;
            width: 100%;
        }

        .item-name {
            font-size: 14px;
            margin-bottom: 2px;
            padding-right: 20px; /* Chừa chỗ cho nút xóa nếu cần */
        }

        .item-type { font-size: 10px; padding: 1px 8px; margin-bottom: 4px; }

        /* CONTROLS TRÊN MOBILE */
        .item-controls {
            grid-column: 2; /* Nằm ở cột nội dung */
            flex-direction: row; /* Dàn ngang: [Qty] [Price] */
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 0;
            margin-top: 4px;
        }

        .quantity-control { height: 32px; }
        .qty-btn { width: 24px; height: 24px; font-size: 12px; }
        .qty-input { width: 24px; font-size: 13px; }

        .item-subtotal {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .subtotal-label { display: none; }
        .subtotal-amount { color: #d0021b; font-size: 15px; }

        /* Nút xóa: Đưa lên góc trên cùng bên phải của item */
        .remove-btn {
            position: absolute;
            top: 0; right: 0;
            color: #999;
            padding: 0;
        }
    }
</style>
