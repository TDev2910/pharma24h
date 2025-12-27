@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="page-wrapper bg-light min-vh-100">
    <section class="checkout-header py-4 bg-white shadow-sm mb-4" style="margin-top:100px;">
        <div class="container">
            <div class="d-flex align-items-center text-muted small mb-2">
                <i class="pi pi-home me-2"></i> Trang chủ
                <i class="pi pi-angle-right mx-2"></i> Giỏ hàng
                <i class="pi pi-angle-right mx-2"></i> Thanh toán
            </div>
            <h2 class="fw-bold text-primary m-0">Thanh Toán & Đặt Hàng</h2>
        </div>
    </section>

    <section class="checkout-content pb-5">
        <div class="container">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                <h5 class="fw-bold text-dark"><i class="pi pi-id-card text-primary me-2"></i>Thông tin nhận hàng</h5>
                            </div>
                            <div class="card-body p-4">
                                <label class="form-label fw-medium mb-2 text-muted">Vui lòng chọn hình thức nhận hàng:</label>
                                <div class="delivery-toggle d-flex justify-content-center mb-4 p-1 bg-light rounded-pill border">
                                    <label class="toggle-item flex-fill">
                                        <input class="delivery-method" type="radio" name="delivery_method" id="delivery_shipping" value="shipping"
                                            {{ old('delivery_method', 'shipping') === 'shipping' ? 'checked' : '' }}>
                                        <div class="inner d-flex align-items-center justify-content-center gap-2 fw-bold">
                                            <i class="pi pi-truck"></i> Giao hàng tận nơi
                                        </div>
                                    </label>
                                    <label class="toggle-item flex-fill">
                                        <input class="delivery-method" type="radio" name="delivery_method" id="delivery_pickup" value="pickup"
                                            {{ old('delivery_method') === 'pickup' ? 'checked' : '' }}>
                                        <div class="inner d-flex align-items-center justify-content-center gap-2 fw-bold">
                                            <i class="pi pi-building"></i> Nhận tại nhà thuốc
                                        </div>
                                    </label>
                                </div>

                                <hr class="text-muted opacity-25 my-4">

                                <div id="shipping_info" class="animate-fade {{ old('delivery_method') === 'pickup' ? 'd-none' : '' }}">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-user me-2"></i>Thông tin người nhận</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                                id="customer_name" name="customer_name"
                                                value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                                {{ old('delivery_method', 'shipping') === 'shipping' ? 'required' : '' }}>
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                                id="customer_phone" name="customer_phone"
                                                value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                                {{ old('delivery_method', 'shipping') === 'shipping' ? 'required' : '' }}>
                                            @error('customer_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Email <span class="text-muted fw-normal">(Nhận hóa đơn điện tử)</span></label>
                                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                                id="customer_email" name="customer_email"
                                                value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                                placeholder="Nhập email của bạn">
                                            @error('customer_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-map me-2"></i>Địa chỉ giao hàng</h6>
                                    <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                                <select class="form-select @error('province') is-invalid @enderror" id="province" name="province">
                                                    <option value="">Chọn tỉnh/thành phố</option>
                                                </select>
                                                @error('province')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Quận / Huyện <span class="text-danger">*</span></label>
                                                <select class="form-select @error('district') is-invalid @enderror" id="district" name="district">
                                                    <option value="">Chọn quận/huyện</option>
                                                </select>
                                                @error('district')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Phường / Xã <span class="text-danger">*</span></label>
                                                <select class="form-select @error('ward') is-invalid @enderror" id="ward" name="ward">
                                                    <option value="">Chọn phường/xã</option>
                                                </select>
                                                @error('ward')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('shipping_address') is-invalid @enderror"
                                                    id="shipping_address" name="shipping_address" placeholder="Số nhà, tên đường, tòa nhà..."
                                                    value="{{ old('shipping_address') }}">
                                                @error('shipping_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                    </div>
                                    <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id') }}">
                                    <input type="hidden" id="ward_code" name="ward_code" value="{{ old('ward_code') }}">
                                    <input type="hidden" id="shipping_fee" name="shipping_fee" value="0">
                                </div>

                                <div id="pickup_info" class="animate-fade {{ old('delivery_method') !== 'pickup' ? 'd-none' : '' }}">
                                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-4 rounded-3">
                                        <div class="d-flex">
                                            <i class="pi pi-info-circle me-2 mt-1"></i>
                                            <div>
                                                <strong>Lưu ý:</strong> Sản phẩm sẽ được giữ tại cửa hàng trong vòng <strong>24h</strong>. Vui lòng đến đúng giờ.
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-user me-2"></i>Thông tin người đến lấy hàng</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name_pickup"
                                                value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_phone_pickup"
                                                value="{{ old('customer_phone', auth()->user()->phone ?? '') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Email <span class="text-muted fw-normal">(Nhận xác nhận đơn hàng)</span></label>
                                            <input type="email" class="form-control" id="customer_email_pickup"
                                                value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                                placeholder="Nhập email của bạn">
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 text-primary"><i class="pi pi-building me-2"></i>Chọn cửa hàng gần bạn</h6>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Tìm kiếm nhà thuốc <span class="text-danger">*</span></label>
                                        <select class="form-select @error('pickup_location') is-invalid @enderror"
                                            id="pickup_location" name="pickup_location">
                                            <option value="">Chọn cửa hàng...</option>
                                            @foreach($pharmacyLocations as $location)
                                                <option value="{{ $location['name'] }}" {{ old('pickup_location') == $location['name'] ? 'selected' : '' }}>
                                                    {{ $location['name'] }} - {{ $location['address'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pickup_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-body p-4">
                                <label class="form-label fw-medium"><i class="pi pi-pencil text-primary me-2"></i>Ghi chú đơn hàng</label>
                                <textarea name="note" id="note" rows="2" class="form-control @error('note') is-invalid @enderror"
                                    placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                <h5 class="fw-bold text-dark"><i class="pi pi-wallet text-primary me-2"></i>Phương thức thanh toán</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="payment-options d-flex flex-column gap-3">
                                    <label class="payment-item d-flex align-items-center p-3 border rounded-3 cursor-pointer transition-all
                                        {{ old('payment_method', 'cod') === 'cod' ? 'border-primary bg-primary-subtle' : '' }}">
                                        <input class="form-check-input me-2" type="radio" name="payment_method" id="payment_cod" value="cod"
                                            {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                        <div class="ms-2 flex-fill">
                                            <span class="fw-bold d-block text-dark">Thanh toán khi nhận hàng (COD)</span>
                                            <span class="small text-muted">Bạn chỉ phải thanh toán khi đã nhận được hàng</span>
                                        </div>
                                        <i class="pi pi-money-bill fs-4 text-success"></i>
                                    </label>

                                    <label class="payment-item d-flex align-items-center p-3 border rounded-3 cursor-pointer transition-all
                                        {{ old('payment_method') === 'vnpay' ? 'border-primary bg-primary-subtle' : '' }}">
                                        <input class="form-check-input me-2" type="radio" name="payment_method" id="payment_vnpay" value="vnpay"
                                            {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}>
                                        <div class="ms-2 flex-fill">
                                            <span class="fw-bold d-block text-dark">Thanh toán qua VNPAY / Ngân hàng</span>
                                            <span class="small text-muted">Quét mã QR để thanh toán nhanh chóng</span>
                                        </div>
                                        <i class="pi pi-credit-card fs-4 text-primary"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="checkout-summary card border-0 shadow-lg rounded-3 position-sticky" style="top:20px;">
                            <div class="card-header bg-primary text-white py-3 rounded-top-3">
                                <h5 class="m-0 fw-bold text-center">Tóm Tắt Đơn Hàng</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="order-items mb-3 border-bottom pb-3">
                                    @foreach($cartItems as $item)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark border me-2">{{ $item->quantity ?? $item->qty ?? 1 }}x</span>
                                                <span class="text-dark small fw-medium text-truncate" style="max-width: 160px;">
                                                    {{ $item->product_name ?? $item->name ?? 'Sản phẩm' }}
                                                </span>
                                            </div>
                                            <span class="small fw-bold">{{ number_format($item->price ?? $item->unit_price ?? 0) }}đ</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính:</span>
                                    <span class="fw-bold">{{ number_format($cartTotal) }}đ</span>
                                </div>
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí vận chuyển:</span>
                                    <span class="text-success fw-bold" id="shipping-fee-display">0đ</span>
                                </div>

                                <hr class="my-3 text-muted">

                                <div class="total-row d-flex justify-content-between align-items-center mb-4">
                                    <span class="h5 fw-bold text-dark m-0">Tổng cộng:</span>
                                    <span class="h4 fw-bold text-danger m-0" id="total-order-display">{{ number_format($cartTotal) }}đ</span>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="agree_terms" checked>
                                    <label class="form-check-label small text-muted" for="agree_terms">
                                        Đồng ý với các <a href="#" class="text-decoration-none">điều khoản & chính sách</a> của nhà thuốc.
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center">
                                    <span>HOÀN TẤT ĐẶT HÀNG</span>
                                </button>
                                <a href="{{ route('cart.index') }}" class="d-block text-center mt-3 text-muted text-decoration-none small hover-underline">
                                    <i class="pi pi-arrow-left me-1"></i> Quay lại giỏ hàng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .delivery-toggle .toggle-item {
        border-radius: 999px;
        border: 1px solid transparent;
        transition: all .2s ease;
    }
    .delivery-toggle .toggle-item input {
        display: none;
    }
    .delivery-toggle .toggle-item .inner {
        padding: 12px 8px;
        border-radius: 999px;
    }
    .delivery-toggle .toggle-item:has(input:checked) .inner {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
    }
    .delivery-toggle .toggle-item:not(:has(input:checked)) .inner {
        color: #6c757d;
    }
    .payment-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
    }
    .animate-fade {
        animation: fadeIn .4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity:0; transform: translateY(-10px); }
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
    const deliveryMethods = document.querySelectorAll('.delivery-method');
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

    deliveryMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'shipping') {
                shippingInfo.classList.remove('d-none');
                pickupInfo.classList.add('d-none');

                // Thêm required
                ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address']
                    .forEach(id => document.getElementById(id).setAttribute('required', 'required'));
                document.getElementById('pickup_location').removeAttribute('required');

                const fields = ['name', 'phone', 'email'];
                fields.forEach(f => {
                    const val = document.getElementById(`customer_${f}_pickup`).value;
                    if(val) document.getElementById(`customer_${f}`).value = val;
                });

            } else {
                shippingInfo.classList.add('d-none');
                pickupInfo.classList.remove('d-none');

                // Xóa required
                ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address']
                    .forEach(id => document.getElementById(id).removeAttribute('required'));
                document.getElementById('pickup_location').setAttribute('required', 'required');


                const fields = ['name', 'phone', 'email'];
                fields.forEach(f => {
                    const val = document.getElementById(`customer_${f}`).value;
                    if(val) document.getElementById(`customer_${f}_pickup`).value = val;
                });
            }
            // Cập nhật hiển thị phí
            updateFeeDisplay(this.value);
        });
    });

    // API địa chỉ VN
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

            const savedProvince = "{{ old('province') }}";
            if(savedProvince) {
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

    //Hàm tính phí vận chuyển từ Server trực tiếp đến GHN
    async function calculateShippingFee(districtId, wardCode) {
        const feeDisplay = document.getElementById('shipping-fee-display');
        const totalDisplay = document.getElementById('total-order-display');

        feeDisplay.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang tính...';

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

                // Cập nhật UI
                if (fee === 0) {
                    feeDisplay.innerHTML = '<span class="text-success">Miễn phí vận chuyển</span>';
                } else {
                    feeDisplay.innerText = formatCurrency(fee);
                }

                // Cập nhật tổng tiền
                const finalTotal = cartSubtotal + fee;
                totalDisplay.innerText = formatCurrency(finalTotal);

                // Lưu vào hidden input
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

    //Map ID GHN và Gọi tính phí
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
                // Lưu ID
                const districtId = result.data.district_id;
                const wardCode = result.data.ward_code;

                document.getElementById('district_id').value = districtId;
                document.getElementById('ward_code').value = wardCode;

                // === GỌI HÀM TÍNH PHÍ NGAY SAU KHI MAP XONG ===
                calculateShippingFee(districtId, wardCode);

                return true;
            }
        } catch (error) {
            console.error('Lỗi map địa chỉ:', error);
        }
    }

    // Init
    loadProvinces();

    // Events change giữ nguyên logic cũ
    document.getElementById('province').addEventListener('change', function() {
        const code = this.options[this.selectedIndex].dataset.code;
        if(code) loadDistricts(code);
        // Reset
        document.getElementById('district_id').value = '';
        document.getElementById('ward_code').value = '';
        document.getElementById('shipping-fee-display').innerText = '0đ';
        document.getElementById('total-order-display').innerText = formatCurrency(cartSubtotal);
    });

    document.getElementById('district').addEventListener('change', function() {
        const code = this.options[this.selectedIndex].dataset.code;
        if(code) loadWards(code);
        // Reset
        document.getElementById('ward_code').value = '';
    });

    // Khi chọn Phường/Xã -> Map ID -> Tính phí
    document.getElementById('ward').addEventListener('change', function() {
        const pName = document.getElementById('province').value;
        const dName = document.getElementById('district').value;
        const wName = this.value;

        if (pName && dName && wName) {
            mapToGHNIds(pName, dName, wName);
        }
    });

    // Submit form validation (Giữ nguyên)
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        // ... Logic validate cũ ...
    });
});
</script>
@endpush
