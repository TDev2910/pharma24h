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
                                    <span class="text-success fw-bold">0đ</span>
                                </div>

                                <hr class="my-3 text-muted">

                                <div class="total-row d-flex justify-content-between align-items-center mb-4">
                                    <span class="h5 fw-bold text-dark m-0">Tổng cộng:</span>
                                    <span class="h4 fw-bold text-danger m-0">{{ number_format($cartTotal) }}đ</span>
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
    // Xử lý chọn phương thức giao hàng
    const deliveryMethods = document.querySelectorAll('.delivery-method');
    const shippingInfo = document.getElementById('shipping_info');
    const pickupInfo = document.getElementById('pickup_info');
    
    deliveryMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'shipping') {
                shippingInfo.classList.remove('d-none');
                pickupInfo.classList.add('d-none');
                
                // Thêm required cho các trường shipping
                document.getElementById('customer_name').setAttribute('required', 'required');
                document.getElementById('customer_phone').setAttribute('required', 'required');
                document.getElementById('province').setAttribute('required', 'required');
                document.getElementById('district').setAttribute('required', 'required');
                document.getElementById('ward').setAttribute('required', 'required');
                document.getElementById('shipping_address').setAttribute('required', 'required');
                
                // Xóa required cho các trường pickup
                document.getElementById('pickup_location').removeAttribute('required');
                
                // Copy dữ liệu
                const customerNamePickup = document.getElementById('customer_name_pickup').value;
                const customerPhonePickup = document.getElementById('customer_phone_pickup').value;
                const customerEmailPickup = document.getElementById('customer_email_pickup').value;
                
                if (customerNamePickup) {
                    document.getElementById('customer_name').value = customerNamePickup;
                }
                
                if (customerPhonePickup) {
                    document.getElementById('customer_phone').value = customerPhonePickup;
                }
                
                if (customerEmailPickup) {
                    document.getElementById('customer_email').value = customerEmailPickup;
                }
            } else {
                shippingInfo.classList.add('d-none');
                pickupInfo.classList.remove('d-none');
                
                // Xóa required cho các trường shipping
                document.getElementById('customer_name').removeAttribute('required');
                document.getElementById('customer_phone').removeAttribute('required');
                document.getElementById('province').removeAttribute('required');
                document.getElementById('district').removeAttribute('required');
                document.getElementById('ward').removeAttribute('required');
                document.getElementById('shipping_address').removeAttribute('required');
                
                // Thêm required cho các trường pickup
                document.getElementById('pickup_location').setAttribute('required', 'required');
                
                // Copy dữ liệu
                const customerName = document.getElementById('customer_name').value;
                const customerPhone = document.getElementById('customer_phone').value;
                const customerEmail = document.getElementById('customer_email').value;
                
                if (customerName) {
                    document.getElementById('customer_name_pickup').value = customerName;
                }
                
                if (customerPhone) {
                    document.getElementById('customer_phone_pickup').value = customerPhone;
                }
                
                if (customerEmail) {
                    document.getElementById('customer_email_pickup').value = customerEmail;
                }
            }
        });
    });
    
    // API địa chỉ từ provinces.open-api.vn (giống Create.vue)
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
            
            // Chọn lại province đã lưu nếu có
            const savedProvince = "{{ old('province') }}";
            if (savedProvince) {
                const options = provinceSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === savedProvince) {
                        provinceSelect.selectedIndex = i;
                        const provinceCode = options[i].dataset.code;
                        if (provinceCode) {
                            loadDistricts(provinceCode);
                        }
                        break;
                    }
                }
            }
        } catch (error) {
            console.error('Lỗi khi lấy danh sách tỉnh/thành phố:', error);
            alert('Không thể tải danh sách tỉnh/thành phố. Vui lòng thử lại.');
        }
    }
    
    // Hàm lấy danh sách quận/huyện từ provinces.open-api.vn
    async function loadDistricts(provinceCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            const data = await response.json();
            const districtSelect = document.getElementById('district');
            
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            
            if (data.districts && Array.isArray(data.districts)) {
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.name;
                    option.dataset.code = district.code;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            }
            
            // Chọn lại district đã lưu nếu có
            const savedDistrict = "{{ old('district') }}";
            if (savedDistrict) {
                const options = districtSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === savedDistrict) {
                        districtSelect.selectedIndex = i;
                        const districtCode = options[i].dataset.code;
                        if (districtCode) {
                            loadWards(districtCode);
                        }
                        break;
                    }
                }
            }
        } catch (error) {
            console.error('Lỗi khi lấy danh sách quận/huyện:', error);
            alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại.');
        }
    }
    
    // Hàm lấy danh sách phường/xã từ provinces.open-api.vn
    async function loadWards(districtCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            const data = await response.json();
            const wardSelect = document.getElementById('ward');
            
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            
            if (data.wards && Array.isArray(data.wards)) {
                data.wards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.name;
                    option.dataset.code = ward.code;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });
            }
            
            // Chọn lại ward đã lưu nếu có
            const savedWard = "{{ old('ward') }}";
            if (savedWard) {
                const options = wardSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === savedWard) {
                        wardSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        } catch (error) {
            console.error('Lỗi khi lấy danh sách phường/xã:', error);
            alert('Không thể tải danh sách phường/xã. Vui lòng thử lại.');
        }
    }
    
    // Hàm map địa chỉ sang GHN ID (gọi khi user chọn ward)
    async function mapToGHNIds(provinceName, districtName, wardName) {
        try {
            // Gọi API backend để map tên địa chỉ sang GHN ID
            const response = await fetch('{{ route("ghn.map-address") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    province: provinceName,
                    district: districtName,
                    ward: wardName
                })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                const errorMessage = result.message || `HTTP error! status: ${response.status}`;
                console.error('GHN Map Address Error:', {
                    status: response.status,
                    message: errorMessage,
                    result: result
                });
                throw new Error(errorMessage);
            }
            
            if (result.success && result.data) {
                // Lưu district_id và ward_code vào hidden fields
                if (result.data.district_id) {
                    document.getElementById('district_id').value = result.data.district_id;
                    console.log('✅ Đã lưu district_id:', result.data.district_id);
                }
                if (result.data.ward_code) {
                    document.getElementById('ward_code').value = result.data.ward_code;
                    console.log('✅ Đã lưu ward_code:', result.data.ward_code);
                }
                return true;
            } else {
                const errorMessage = result.message || 'Không thể map địa chỉ sang GHN ID';
                console.error('GHN Map Address Failed:', result);
                throw new Error(errorMessage);
            }
        } catch (error) {
            console.error('Lỗi khi map địa chỉ sang GHN ID:', error);
            throw error; // Throw error để caller có thể xử lý
        }
    }
    
    // Khởi tạo
    loadProvinces();
    
    // Sự kiện khi chọn tỉnh/thành phố
    document.getElementById('province').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const provinceCode = selectedOption.dataset.code;
        if (provinceCode) {
            loadDistricts(provinceCode);
        }
        // Reset district và ward
        document.getElementById('district_id').value = '';
        document.getElementById('ward_code').value = '';
    });
    
    // Sự kiện khi chọn quận/huyện
    document.getElementById('district').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const districtCode = selectedOption.dataset.code;
        if (districtCode) {
            loadWards(districtCode);
        }
        // Reset ward_code
        document.getElementById('ward_code').value = '';
    });
    
    // Sự kiện khi chọn phường/xã - Map sang GHN ID
    document.getElementById('ward').addEventListener('change', function() {
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.value;
        const districtName = districtSelect.options[districtSelect.selectedIndex]?.value;
        const wardName = wardSelect.options[wardSelect.selectedIndex]?.value;
        
        if (provinceName && districtName && wardName) {
            // Map sang GHN ID
            mapToGHNIds(provinceName, districtName, wardName);
        }
    });
    
    // Form Validation
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
        let isValid = true;
        
        if (deliveryMethod === 'shipping') {
            // Xử lý khi giao hàng tận nơi
            const requiredFields = ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address'];
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin giao hàng');
                return;
            }
            
            // Kiểm tra và map district_id, ward_code nếu chưa có
            const districtId = document.getElementById('district_id').value;
            const wardCode = document.getElementById('ward_code').value;
            
            if (!districtId || !wardCode) {
                e.preventDefault();
                
                // Lấy thông tin địa chỉ đã chọn
                const provinceSelect = document.getElementById('province');
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');
                
                const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.value;
                const districtName = districtSelect.options[districtSelect.selectedIndex]?.value;
                const wardName = wardSelect.options[wardSelect.selectedIndex]?.value;
                
                if (provinceName && districtName && wardName) {
                    // Hiển thị thông báo đang xử lý
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                    
                    try {
                        // Map địa chỉ sang GHN ID
                        const mapped = await mapToGHNIds(provinceName, districtName, wardName);
                        
                        if (mapped) {
                            // Kiểm tra lại sau khi map
                            const newDistrictId = document.getElementById('district_id').value;
                            const newWardCode = document.getElementById('ward_code').value;
                            
                            if (newDistrictId && newWardCode) {
                                // Log để debug
                                console.log('✅ Đã map thành công:', {
                                    district_id: newDistrictId,
                                    ward_code: newWardCode
                                });
                                
                                // Submit form lại
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                                
                                // Đảm bảo hidden fields có giá trị trước khi submit
                                const districtIdField = document.getElementById('district_id');
                                const wardCodeField = document.getElementById('ward_code');
                                
                                if (!districtIdField.value || !wardCodeField.value) {
                                    console.error('❌ Hidden fields không có giá trị!', {
                                        district_id: districtIdField.value,
                                        ward_code: wardCodeField.value
                                    });
                                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                                    return;
                                }
                                
                                console.log('📤 Đang submit form với:', {
                                    district_id: districtIdField.value,
                                    ward_code: wardCodeField.value
                                });
                                
                                this.submit();
                            } else {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                                alert('Không thể lấy thông tin địa chỉ từ GHN. Vui lòng thử lại hoặc liên hệ hỗ trợ.');
                            }
                        }
                    } catch (error) {
                        console.error('Error mapping address:', error);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        const errorMessage = error.message || 'Có lỗi xảy ra khi xử lý địa chỉ. Vui lòng thử lại.';
                        alert(errorMessage);
                    }
                } else {
                    alert('Vui lòng chọn đầy đủ tỉnh/thành phố, quận/huyện và phường/xã.');
                }
                return;
            }
        } else {
            // Xử lý khi nhận tại nhà thuốc
            // Copy thông tin người nhận từ form pickup sang form chính
            document.getElementById('customer_name').value = document.getElementById('customer_name_pickup').value;
            document.getElementById('customer_phone').value = document.getElementById('customer_phone_pickup').value;
            document.getElementById('customer_email').value = document.getElementById('customer_email_pickup').value;
            
            // Kiểm tra thông tin nhận hàng tại nhà thuốc
            const pickupFields = ['customer_name_pickup', 'customer_phone_pickup', 'pickup_location'];
            
            pickupFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    isValid = false;
                    if (field === 'pickup_location') {
                        input.classList.add('is-invalid');
                    } else {
                        // Highlight các trường khác nếu cần
                        input.classList.add('border-danger');
                    }
                } else {
                    if (field === 'pickup_location') {
                        input.classList.remove('is-invalid');
                    } else {
                        input.classList.remove('border-danger');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin nhận hàng tại nhà thuốc');
                return;
            }
        }
        
        // Kiểm tra điều khoản
        if (!document.getElementById('agree_terms').checked) {
            e.preventDefault();
            alert('Vui lòng đồng ý với điều khoản & điều kiện');
            return;
        }
    });
});
</script>
@endpush