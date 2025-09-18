@extends('layouts.admin')

@section('title', 'Dashboard Quản lý khách hàng')

@section('content')
<div class="crm-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="header-left">
            <div class="date-selector">
                <span class="date-text">Quản lý khách hàng</span>
            </div>
            <p class="header-subtitle">Quản lý thông tin khách hàng của hệ thống</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stats-card">
            <div class="stats-card-inner">
                <div class="stats-icon" style="background: #4F46E5;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stats-label">Tổng khách hàng</div>
                    <div class="stats-number">{{ $totalCustomers }}</div>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-card-inner">
                <div class="stats-icon" style="background: #10B981;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <div class="stats-label">Khách hàng mới</div>
                    <div class="stats-number">{{ $newCustomers }}</div>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-card-inner">
                <div class="stats-icon" style="background: #F59E0B;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="stats-label">Khách hàng hoạt động</div>
                    <div class="stats-number">{{ $totalCustomers }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Data Table -->
    <div class="table-section">
        <div class="table-header">
            <h3 class="table-title">Danh sách dữ liệu khách hàng</h3>
            <div class="table-actions">
                <button class="action-btn outline">
                    <i class="fas fa-download"></i>
                    Export CSV
                </button>
                <button type="button" class="action-btn primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus"></i>
                    Thêm khách hàng
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-controls">
                <button class="btn-filter">
                    <i class="fas fa-filter"></i>
                    Lọc
                </button>
                <select class="status-select">
                    <option>Tất cả trạng thái</option>
                    <option>Hoạt động</option>
                    <option>Không hoạt động</option>
                </select>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Tìm kiếm khách hàng...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="customers-table">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Tên khách hàng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th class="text-center">Tổng số đơn hàng đã mua</th>
                        <th class="text-center">Tổng số tiền đã chi tiêu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="customer-avatar">
                                    @if($customer->avatar)
                                        <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ Str::substr($customer->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="customer-name">{{ $customer->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span>{{ $customer->email ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span>{{ $customer->phone ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span>{{ $customer->address ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span>
                                    {{ optional($customer->orders)->count() ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span>
                                    {{ number_format(optional($customer->orders)->sum('total_amount') ?? 0, 0, ',', '.') }} đ
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn action-btn-sm btn-detail" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button"
                                        class="action-btn action-btn-sm btn-edit edit-user-btn"
                                        title="Sửa"
                                        data-user='{{ json_encode($customer) }}'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline delete-form" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-sm btn-delete" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Không có dữ liệu khách hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Include modal thêm khách hàng -->
@include('admin.customer.modals.createUser')
@include('admin.customer.modals.editUsers')
<style>
/* CRM Dashboard Styles */
.crm-dashboard {
    padding: 24px;
    background: #f8fafc;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Section */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-left {
    flex: 1;
}

.date-selector {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    cursor: pointer;
}

.date-text {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
}

.date-selector i {
    color: #6b7280;
    font-size: 14px;
}

.header-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

/* Stats Card Section */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 32px;
}

.stats-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stats-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.stats-card-inner {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.stats-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 4px;
}

.stats-number {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

/* Customer Cell */
.customer-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    background-color: #e5e7eb;
}

.customer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
}

.customer-name {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
}

/* Action Group */
.action-group {
    display: flex;
    gap: 6px;
    justify-content: center;
    align-items: center;
}

.action-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
}

.action-btn.outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #6b7280;
}

.action-btn.primary {
    background: #4F46E5;
    color: white;
}

.action-btn.action-btn-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #4F46E5;
    font-size: 16px;
    transition: background 0.15s, color 0.15s, border 0.15s;
    cursor: pointer;
}

.action-btn.action-btn-sm:hover,
.action-btn.action-btn-sm:focus {
    background: #f3f4f6;
    color: #4338ca;
    border-color: #c7d2fe;
}

.btn-detail {
    color: #4F46E5;
}

.btn-edit {
    color: #10B981;
}

.btn-delete {
    color: #EF4444;
}

.btn-detail:hover { background: #eef2ff; }
.btn-edit:hover { background: #d1fae5; }
.btn-delete:hover { background: #fee2e2; }

.delete-form {
    display: inline;
}

/* Table Container */
.table-container {
    width: 100%;
    overflow-x: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-top: 16px;
}

.customers-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.customers-table th, .customers-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f3f5;
    text-align: left;
    vertical-align: middle;
    font-size: 15px;
}

.customers-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.customers-table tbody tr:hover {
    background: #f6f8fa;
}

.customer-avatar, .avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
    color: #fff;
    font-weight: 600;
    font-size: 16px;
}

.customer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.action-group {
    display: flex;
    gap: 8px;
}

/* Table Header */
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.table-actions {
    display: flex;
    gap: 12px;
}

/* Filter Section */
.filter-section {
    margin-bottom: 16px;
}

.filter-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-filter {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
}

.status-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: #fff;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    padding: 8px 32px 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.search-icon {
    position: absolute;
    right: 10px;
    color: #9ca3af;
    pointer-events: none;
}

.customers-table th.text-center,
.customers-table td.text-center {
    text-align: center !important;
}

/* Giữ nguyên các CSS khác */
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form thêm khách hàng
    const createForm = document.getElementById('createUserForm');
    
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset validation errors
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btnSaveUser');
            
            // Disable button while submitting
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || 'Thêm khách hàng thành công!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Đóng modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                        modal.hide();
                        
                        // Reset form
                        createForm.reset();
                        
                        // Tải lại trang để hiển thị khách hàng mới
                        window.location.reload();
                    });
                } else if (data.errors) {
                    // Hiển thị lỗi validation
                    Object.keys(data.errors).forEach(key => {
                        const errorMsg = data.errors[key][0];
                        const inputField = document.getElementById(key);
                        const errorDisplay = document.getElementById(key + '-error');
                        
                        if (inputField && errorDisplay) {
                            inputField.classList.add('is-invalid');
                            errorDisplay.textContent = errorMsg;
                        }
                    });
                    
                    // Thông báo lỗi chung
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Vui lòng kiểm tra lại thông tin đã nhập',
                    });
                } else if (data.error) {
                    // Hiển thị lỗi hệ thống
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.error,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi hệ thống!',
                    text: 'Có lỗi xảy ra khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.',
                });
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Lưu khách hàng';
            });
        });
    }
    
    // Hiển thị thông báo từ session flash
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session("success") }}',
            showConfirmButton: false,
            timer: 1500
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: '{{ session("error") }}',
        });
    @endif
    
    document.getElementById('createUserModal').addEventListener('shown.bs.modal', function () {
        loadAdminProvinces();
    });

    function loadAdminProvinces() {
        fetch('https://provinces.open-api.vn/api/?depth=1')
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('admin-province');
                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
                
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.code;
                    option.textContent = province.name;
                    provinceSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading provinces:', error);
                document.getElementById('admin-province').innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    function loadAdminDistricts(provinceCode) {
        const districtSelect = document.getElementById('admin-district');
        const wardSelect = document.getElementById('admin-ward');

        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
        wardSelect.disabled = true;

        if (!provinceCode) {
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.disabled = false;
                districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
                
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.code;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading districts:', error);
                districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                districtSelect.disabled = false;
            });
    }

    function loadAdminWards(districtCode) {
        const wardSelect = document.getElementById('admin-ward');

        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        if (!districtCode) {
            wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
            return;
        }

        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.disabled = false;
                wardSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
                
                data.wards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.code;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading wards:', error);
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                wardSelect.disabled = false;
            });
    }

    // Event listeners for admin address selects
    document.getElementById('admin-province').addEventListener('change', function() {
        loadAdminDistricts(this.value);
    });

    document.getElementById('admin-district').addEventListener('change', function() {
        loadAdminWards(this.value);
    });

    // Event listener for edit buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-user-btn')) {
            e.preventDefault();
            const button = e.target.closest('.edit-user-btn');
            const userData = JSON.parse(button.getAttribute('data-user'));
            console.log('Edit button clicked, user data:', userData);
            
            if (typeof openEditUserModal === 'function') {
                openEditUserModal(userData);
            } else {
                console.error('openEditUserModal function not found');
                alert('Chức năng chỉnh sửa chưa sẵn sàng. Vui lòng tải lại trang.');
            }
        }
    });

});
</script>
@endsection