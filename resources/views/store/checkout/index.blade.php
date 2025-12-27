@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="checkout-page bg-light min-vh-100 pb-5" style="padding-top: 100px;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small text-muted">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>

        <h2 class="h4 fw-bold text-primary mb-4">Thanh toán & Đặt hàng</h2>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold m-0"><i class="pi pi-truck me-2 text-primary"></i>Hình thức nhận hàng</h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="delivery-option-card {{ old('delivery_method', 'shipping') === 'shipping' ? 'active' : '' }}">
                                        <input type="radio" name="delivery_method" class="delivery-method" value="shipping"
                                            {{ old('delivery_method', 'shipping') === 'shipping' ? 'checked' : '' }}>
                                        <div class="content">
                                            <i class="pi pi-home fs-4 mb-2"></i>
                                            <span>Giao tận nơi</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="delivery-option-card {{ old('delivery_method') === 'pickup' ? 'active' : '' }}">
                                        <input type="radio" name="delivery_method" class="delivery-method" value="pickup"
                                            {{ old('delivery_method') === 'pickup' ? 'checked' : '' }}>
                                        <div class="content">
                                            <i class="pi pi-map-marker fs-4 mb-2"></i>
                                            <span>Nhận tại nhà thuốc</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold m-0"><i class="pi pi-user me-2 text-primary"></i>Thông tin chi tiết</h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                            id="customer_name" name="customer_name" placeholder="Họ tên"
                                            value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                                        <label for="customer_name">Họ và tên <span class="text-danger">*</span></label>
                                        @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                            id="customer_phone" name="customer_phone" placeholder="SĐT"
                                            value="{{ old('customer_phone', auth()->user()->phone ?? '') }}">
                                        <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                        @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                            id="customer_email" name="customer_email" placeholder="Email"
                                            value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                                        <label for="customer_email">Email (Nhận hóa đơn)</label>
                                        @error('customer_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="shipping_info" class="animate-fade {{ old('delivery_method') === 'pickup' ? 'd-none' : '' }}">
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <h6 class="small fw-bold text-muted mb-3 text-uppercase">Địa chỉ giao hàng</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <select class="form-select py-3 @error('province') is-invalid @enderror" id="province" name="province">
                                                <option value="">Tỉnh/Thành phố</option>
                                            </select>
                                            @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select py-3 @error('district') is-invalid @enderror" id="district" name="district">
                                                <option value="">Quận/Huyện</option>
                                            </select>
                                            @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-select py-3 @error('ward') is-invalid @enderror" id="ward" name="ward">
                                                <option value="">Phường/Xã</option>
                                            </select>
                                            @error('ward') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control @error('shipping_address') is-invalid @enderror"
                                                    id="shipping_address" name="shipping_address" placeholder="Địa chỉ"
                                                    value="{{ old('shipping_address') }}">
                                                <label for="shipping_address">Số nhà, tên đường <span class="text-danger">*</span></label>
                                                @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id') }}">
                                <input type="hidden" id="ward_code" name="ward_code" value="{{ old('ward_code') }}">
                                <input type="hidden" id="shipping_fee" name="shipping_fee" value="0">
                            </div>

                            <div id="pickup_info" class="animate-fade {{ old('delivery_method') !== 'pickup' ? 'd-none' : '' }}">
                                <div class="alert alert-primary d-flex align-items-center mb-3">
                                    <i class="pi pi-info-circle me-2 fs-5"></i>
                                    <div class="small">Vui lòng đến lấy hàng trong vòng <strong>24h</strong> sau khi đặt.</div>
                                </div>
                                <input type="hidden" id="customer_name_pickup">
                                <input type="hidden" id="customer_phone_pickup">
                                <input type="hidden" id="customer_email_pickup">

                                <div class="form-floating mb-3">
                                    <select class="form-select @error('pickup_location') is-invalid @enderror" id="pickup_location" name="pickup_location">
                                        <option value="">Chọn nhà thuốc gần bạn...</option>
                                        @foreach($pharmacyLocations as $location)
                                            <option value="{{ $location['name'] }}" {{ old('pickup_location') == $location['name'] ? 'selected' : '' }}>
                                                {{ $location['name'] }} - {{ $location['address'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="pickup_location">Địa điểm nhận hàng <span class="text-danger">*</span></label>
                                    @error('pickup_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Ghi chú" id="note" name="note" style="height: 80px">{{ old('note') }}</textarea>
                                    <label for="note">Ghi chú cho đơn hàng (không bắt buộc)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-4 section-card">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold m-0"><i class="pi pi-wallet me-2 text-primary"></i>Phương thức thanh toán</h6>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="payment-methods d-flex flex-column gap-3">
                                <label class="payment-option {{ old('payment_method', 'cod') === 'cod' ? 'selected' : '' }}">
                                    <input type="radio" name="payment_method" id="payment_cod" value="cod"
                                        {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                    <div class="d-flex align-items-center w-100">
                                        <div class="icon-wrap bg-light rounded-circle p-2 me-3">
                                            <i class="pi pi-money-bill text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                                            <div class="small text-muted">Thanh toán tiền mặt cho shipper khi nhận hàng</div>
                                        </div>
                                        <i class="pi pi-check-circle ms-auto fs-4 check-icon"></i>
                                    </div>
                                </label>

                                <label class="payment-option {{ old('payment_method') === 'vnpay' ? 'selected' : '' }}">
                                    <input type="radio" name="payment_method" id="payment_vnpay" value="vnpay"
                                        {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}>
                                    <div class="d-flex align-items-center w-100">
                                        <div class="icon-wrap bg-light rounded-circle p-2 me-3">
                                            <i class="pi pi-credit-card text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Thanh toán VNPAY</div>
                                            <div class="small text-muted">Thanh toán qua ngân hàng, ví điện tử VNPAY</div>
                                        </div>
                                        <i class="pi pi-check-circle ms-auto fs-4 check-icon"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 checkout-summary sticky-top" style="top: 20px; z-index: 10;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Đơn hàng của bạn</h5>

                            <div class="order-scrollable mb-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($cartItems as $item)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="position-relative me-3">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="sp" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="pi pi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" style="font-size: 0.7rem;">
                                                {{ $item->quantity }}
                                            </span>
                                        </div>
                                        <div class="flex-fill" style="min-width: 0;">
                                            <div class="text-truncate fw-medium small">{{ $item->product_name ?? $item->name }}</div>
                                            <div class="text-muted small">{{ number_format($item->price ?? 0) }}đ</div>
                                        </div>
                                        <div class="fw-bold small">
                                            {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1)) }}đ
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="dashed">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính</span>
                                <span class="fw-bold">{{ number_format($cartTotal) }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="fw-bold text-success" id="shipping-fee-display">0đ</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-4">
                                <span class="fw-bold fs-5">Tổng cộng</span>
                                <span class="fw-bold fs-4 text-primary" id="total-order-display">{{ number_format($cartTotal) }}đ</span>
                            </div>

                            <div class="form-check mb-3 small">
                                <input class="form-check-input" type="checkbox" id="agree_terms" checked>
                                <label class="form-check-label text-muted" for="agree_terms">
                                    Tôi đồng ý với <a href="#" class="text-primary text-decoration-none">Điều khoản & Điều kiện</a> mua hàng.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm btn-checkout" style="background: #005EB8">
                                Đặt hàng ngay
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted small hover-underline">
                                    <i class="pi pi-arrow-left me-1"></i> Quay lại giỏ hàng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Pharmacity Style Adjustments */
    :root {
        --primary-color: #0d6efd; /* Blue tone */
        --bg-color: #f4f6f8;
    }

    /* Delivery Options Cards */
    .delivery-option-card {
        display: block;
        cursor: pointer;
        position: relative;
    }
    .delivery-option-card input {
        display: none;
    }
    .delivery-option-card .content {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        background: #fff;
        transition: all 0.2s;
        color: #6c757d;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .delivery-option-card input:checked + .content {
        border-color: var(--primary-color);
        background-color: #f0f7ff;
        color: var(--primary-color);
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }

    /* Payment Method Cards */
    .payment-option {
        cursor: pointer;
        display: block;
    }
    .payment-option input {
        display: none;
    }
    .payment-option > div {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.2s;
        background: #fff;
    }
    .payment-option .check-icon {
        opacity: 0;
        transition: all 0.2s;
        color: var(--primary-color);
        transform: scale(0.5);
    }
    .payment-option input:checked + div {
        border-color: var(--primary-color);
        background-color: #f0f7ff;
    }
    .payment-option input:checked + div .check-icon {
        opacity: 1;
        transform: scale(1);
    }

    /* Input Fields */
    .form-control, .form-select {
        border-color: #dee2e6;
        border-radius: 6px;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    /* Summary Section */
    .checkout-summary hr.dashed {
        border-top: 1px dashed #bbb;
        opacity: 1;
    }
    .btn-checkout {
        transition: transform 0.1s;
    }
    .btn-checkout:active {
        transform: scale(0.98);
    }

    /* Scrollbar for order items */
    .order-scrollable::-webkit-scrollbar {
        width: 5px;
    }
    .order-scrollable::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .animate-fade {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity:0; transform: translateY(-5px); }
        to { opacity:1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tổng tiền giỏ hàng ban đầu
    const cartSubtotal = {{ $cartTotal }};

    // Hàm format tiền tệ
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    };

    // Xử lý chọn phương thức giao hàng
    const deliveryInputs = document.querySelectorAll('input[name="delivery_method"]');
    const paymentInputs = document.querySelectorAll('input[name="payment_method"]');

    const shippingInfo = document.getElementById('shipping_info');
    const pickupInfo = document.getElementById('pickup_info');

    // Hàm cập nhật hiển thị phí khi đổi phương thức giao hàng
    function updateFeeDisplay(method) {
        if (method === 'pickup') {
            document.getElementById('shipping-fee-display').innerText = '0đ';
            document.getElementById('total-order-display').innerText = formatCurrency(cartSubtotal);
            document.getElementById('shipping_fee').value = 0;
        } else {
            // Nếu chuyển về shipping, gọi lại hàm tính phí nếu đã có địa chỉ
            const districtId = document.getElementById('district_id').value;
            const wardCode = document.getElementById('ward_code').value;
            if (districtId && wardCode) {
                calculateShippingFee(districtId, wardCode);
            } else {
                document.getElementById('shipping-fee-display').innerText = '0đ';
            }
        }
    }

    // Event Listener cho Delivery Method
    deliveryInputs.forEach(input => {
        input.addEventListener('change', function() {
            // UI Toggle logic
            if (this.value === 'shipping') {
                shippingInfo.classList.remove('d-none');
                pickupInfo.classList.add('d-none');

                // Thêm required cho shipping
                ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address']
                    .forEach(id => document.getElementById(id)?.setAttribute('required', 'required'));
                document.getElementById('pickup_location').removeAttribute('required');

            } else {
                shippingInfo.classList.add('d-none');
                pickupInfo.classList.remove('d-none');

                // Xóa required cho shipping
                ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address']
                    .forEach(id => document.getElementById(id)?.removeAttribute('required'));
                document.getElementById('pickup_location').setAttribute('required', 'required');
            }

            updateFeeDisplay(this.value);
        });
    });

    // Event Listener cho Payment Method (Để cập nhật UI selected class)
    paymentInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Xóa class selected cũ
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            // Thêm class selected cho label cha
            this.closest('.payment-option').classList.add('selected');
        });
    });

    // --- LOGIC API & GHN (GIỮ NGUYÊN) ---

    async function loadProvinces() {
        try {
            const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
            const provinces = await response.json();
            const provinceSelect = document.getElementById('province');

            provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.dataset.code = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });

            // Logic giữ lại giá trị cũ khi validate fail
            const savedProvince = "{{ old('province') }}";
            if(savedProvince) {
                // Bạn có thể thêm logic auto-select ở đây nếu cần, tương tự code cũ
                for(let i=0; i<provinceSelect.options.length; i++) {
                    if(provinceSelect.options[i].value === savedProvince) {
                        provinceSelect.selectedIndex = i;
                        const code = provinceSelect.options[i].dataset.code;
                        loadDistricts(code);
                        break;
                    }
                }
            }
        } catch (error) { console.error(error); }
    }

    async function loadDistricts(provinceCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            const data = await response.json();
            const districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.dataset.code = district.code;
                option.textContent = district.name;
                districtSelect.appendChild(option);
            });
            // Re-select logic for old('district') can go here
        } catch (error) { console.error(error); }
    }

    async function loadWards(districtCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            const data = await response.json();
            const wardSelect = document.getElementById('ward');
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            data.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.name;
                option.dataset.code = ward.code;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            });
        } catch (error) { console.error(error); }
    }

    async function calculateShippingFee(districtId, wardCode) {
        const feeDisplay = document.getElementById('shipping-fee-display');
        const totalDisplay = document.getElementById('total-order-display');

        feeDisplay.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const response = await fetch('{{ route("checkout.get_shipping_fee") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    district_id: districtId,
                    ward_code: wardCode
                })
            });

            const result = await response.json();

            if (result.success) {
                const fee = parseInt(result.fee);
                if (fee === 0) {
                    feeDisplay.innerHTML = '<span class="text-success">Miễn phí vận chuyển</span>';
                } else {
                    feeDisplay.innerText = formatCurrency(fee);
                }
                const finalTotal = cartSubtotal + fee;
                totalDisplay.innerText = formatCurrency(finalTotal);
                document.getElementById('shipping_fee').value = fee;
            } else {
                feeDisplay.innerText = 'Chưa tính được';
                console.error('Lỗi tính phí:', result.message);
            }
        } catch (error) {
            console.error('Lỗi gọi API tính phí:', error);
            feeDisplay.innerText = 'Lỗi';
        }
    }

    async function mapToGHNIds(provinceName, districtName, wardName) {
        try {
            const response = await fetch('{{ route("ghn.map-address") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    province: provinceName,
                    district: districtName,
                    ward: wardName
                })
            });

            const result = await response.json();

            if (result.success && result.data) {
                const districtId = result.data.district_id;
                const wardCode = result.data.ward_code;

                document.getElementById('district_id').value = districtId;
                document.getElementById('ward_code').value = wardCode;

                calculateShippingFee(districtId, wardCode);
                return true;
            }
        } catch (error) {
            console.error('Lỗi map địa chỉ:', error);
        }
    }

    // Init
    loadProvinces();

    // Events change
    document.getElementById('province').addEventListener('change', function() {
        const code = this.options[this.selectedIndex].dataset.code;
        if(code) loadDistricts(code);
        document.getElementById('district_id').value = '';
        document.getElementById('ward_code').value = '';
        document.getElementById('shipping-fee-display').innerText = '0đ';
        document.getElementById('total-order-display').innerText = formatCurrency(cartSubtotal);
    });

    document.getElementById('district').addEventListener('change', function() {
        const code = this.options[this.selectedIndex].dataset.code;
        if(code) loadWards(code);
        document.getElementById('ward_code').value = '';
    });

    document.getElementById('ward').addEventListener('change', function() {
        const pName = document.getElementById('province').value;
        const dName = document.getElementById('district').value;
        const wName = this.value;

        if (pName && dName && wName) {
            mapToGHNIds(pName, dName, wName);
        }
    });

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        if (!document.getElementById('agree_terms').checked) {
            e.preventDefault();
            alert('Vui lòng đồng ý với điều khoản & điều kiện mua hàng.');
        }
    });
});
</script>
@endpush
