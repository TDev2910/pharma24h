@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                        @csrf
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" 
                                id="note" name="note" value="{{ old('note') }}" placeholder="Nhập ghi chú ở đây">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Phương thức nhận hàng -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Hình thức nhận hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input delivery-method" type="radio" name="delivery_method" 
                                                id="delivery_shipping" value="shipping" 
                                                {{ old('delivery_method', 'shipping') === 'shipping' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="delivery_shipping">
                                                Giao hàng tận nơi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input delivery-method" type="radio" name="delivery_method" 
                                                id="delivery_pickup" value="pickup" 
                                                {{ old('delivery_method') === 'pickup' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="delivery_pickup">
                                                Nhận tại nhà thuốc
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Thông tin giao hàng tận nơi -->
                                <div id="shipping_info" class="mt-4 {{ old('delivery_method') === 'pickup' ? 'd-none' : '' }}">
                                    <h6 class="mb-3">Thông tin người nhận hàng</h6>
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                            id="customer_name" name="customer_name" 
                                            value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                            id="customer_phone" name="customer_phone" 
                                            value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select class="form-select @error('province') is-invalid @enderror" id="province" name="province">
                                            <option value="">Chọn tỉnh/thành phố</option>
                                        </select>
                                        @error('province')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                            <select class="form-select @error('district') is-invalid @enderror" id="district" name="district">
                                                <option value="">Chọn quận/huyện</option>
                                            </select>
                                            @error('district')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                            <select class="form-select @error('ward') is-invalid @enderror" id="ward" name="ward">
                                                <option value="">Chọn phường/xã</option>
                                            </select>
                                            @error('ward')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="shipping_address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" 
                                            id="shipping_address" name="shipping_address" placeholder="Nhập số nhà, tên đường" 
                                            value="{{ old('shipping_address') }}">
                                        @error('shipping_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Thông tin nhận tại nhà thuốc -->
                                <div id="pickup_info" class="mt-4 {{ old('delivery_method') !== 'pickup' ? 'd-none' : '' }}">
                                    <h6 class="mb-3">Thông tin người nhận hàng</h6>
                                    <div class="mb-3">
                                        <label for="customer_name_pickup" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                            id="customer_name_pickup" 
                                            value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customer_phone_pickup" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                            id="customer_phone_pickup" 
                                            value="{{ old('customer_phone', auth()->user()->phone ?? '') }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="pickup_location" class="form-label">Chọn cửa hàng <span class="text-danger">*</span></label>
                                        <select class="form-select @error('pickup_location') is-invalid @enderror" 
                                            id="pickup_location" name="pickup_location">
                                            <option value="">Chọn cửa hàng</option>
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
                        
                        <!-- Phương thức thanh toán -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Phương thức thanh toán</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" 
                                        {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_cod">
                                        <i class="fas fa-money-bill-wave me-2"></i> Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay" value="vnpay"
                                        {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_vnpay">
                                        <i class="fas fa-credit-card me-2"></i> Thanh toán qua VNPAY
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Đặt hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chi tiết thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span>{{ number_format($cartTotal) }}đ</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        <span>0đ</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Tổng tiền</span>
                        <span class="text-danger fw-bold fs-5">{{ number_format($cartTotal) }}đ</span>
                    </div>
                    
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree_terms" checked>
                            <label class="form-check-label small" for="agree_terms">
                                Bằng cách tích vào ô này, bạn đồng ý với <a href="#">điều khoản & điều kiện</a> của chúng tôi.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
                
                // Copy dữ liệu
                const customerNamePickup = document.getElementById('customer_name_pickup').value;
                const customerPhonePickup = document.getElementById('customer_phone_pickup').value;
                
                if (customerNamePickup) {
                    document.getElementById('customer_name').value = customerNamePickup;
                }
                
                if (customerPhonePickup) {
                    document.getElementById('customer_phone').value = customerPhonePickup;
                }
            } else {
                shippingInfo.classList.add('d-none');
                pickupInfo.classList.remove('d-none');
                
                // Copy dữ liệu
                const customerName = document.getElementById('customer_name').value;
                const customerPhone = document.getElementById('customer_phone').value;
                
                if (customerName) {
                    document.getElementById('customer_name_pickup').value = customerName;
                }
                
                if (customerPhone) {
                    document.getElementById('customer_phone_pickup').value = customerPhone;
                }
            }
        });
    });
    
    // API địa chỉ
    async function loadProvinces() {
        try {
            const response = await fetch('https://provinces.open-api.vn/api/p/');
            const provinces = await response.json();
            const provinceSelect = document.getElementById('province');
            
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
        }
    }
    
    // Hàm lấy danh sách quận/huyện theo tỉnh/thành phố
    async function loadDistricts(provinceCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            const data = await response.json();
            const districtSelect = document.getElementById('district');
            
            // Xóa các option cũ
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.dataset.code = district.code;
                option.textContent = district.name;
                districtSelect.appendChild(option);
            });
            
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
        }
    }
    
    // Hàm lấy danh sách phường/xã theo quận/huyện
    async function loadWards(districtCode) {
        try {
            const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            const data = await response.json();
            const wardSelect = document.getElementById('ward');
            
            // Xóa các option cũ
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            
            data.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.name;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            });
            
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
    });
    
    // Sự kiện khi chọn quận/huyện
    document.getElementById('district').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const districtCode = selectedOption.dataset.code;
        if (districtCode) {
            loadWards(districtCode);
        }
    });
    
    // Form Validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked').value;
        
        if (deliveryMethod === 'shipping') {
            // Chỉ xử lý khi giao hàng tận nơi
            const requiredFields = ['customer_name', 'customer_phone', 'province', 'district', 'ward', 'shipping_address'];
            let isValid = true;
            
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
            }
        } else {
            // Xử lý khi nhận tại nhà thuốc
            // Copy thông tin người nhận từ form pickup sang form chính
            document.getElementById('customer_name').value = document.getElementById('customer_name_pickup').value;
            document.getElementById('customer_phone').value = document.getElementById('customer_phone_pickup').value;
            
            // Kiểm tra thông tin nhận hàng tại nhà thuốc
            const pickupFields = ['customer_name_pickup', 'customer_phone_pickup', 'pickup_location'];
            let isValid = true;
            
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
            }
        }
        
        // Kiểm tra điều khoản
        if (!document.getElementById('agree_terms').checked) {
            e.preventDefault();
            alert('Vui lòng đồng ý với điều khoản & điều kiện');
        }
    });
});
</script>
@endpush