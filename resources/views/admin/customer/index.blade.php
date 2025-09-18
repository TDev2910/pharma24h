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
                <a href="{{ route('admin.customers.create') }}" class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Thêm khách hàng
                </a>
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
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn action-btn-sm btn-edit " title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
    // Xác nhận xóa
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Bạn có chắc chắn muốn xóa khách hàng này?')) {
                event.preventDefault();
            }
        });
    });
    
    // Tìm kiếm khách hàng
    const searchInput = document.getElementById('searchCustomer');
    const tableRows = document.querySelectorAll('.customers-table tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const customerName = row.querySelector('.customer-name')?.textContent.toLowerCase() || '';
                const customerEmail = row.cells[1]?.textContent.toLowerCase() || '';
                const customerPhone = row.cells[2]?.textContent.toLowerCase() || '';
                const customerAddress = row.cells[3]?.textContent.toLowerCase() || '';
                
                if (customerName.includes(searchTerm) || 
                    customerEmail.includes(searchTerm) || 
                    customerPhone.includes(searchTerm) ||
                    customerAddress.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection