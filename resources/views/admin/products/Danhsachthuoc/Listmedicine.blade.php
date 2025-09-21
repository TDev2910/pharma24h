@extends('layouts.admin')

@section('title', 'Danh Sách Thuốc')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Hanghoa/Danhsachhanghoa/medicine.css') }}">
@endpush

@section('content')

    <!-- Header Control Bar -->
    <div class="header-control-bar">
        <!-- Controls Section -->
        <div class="controls-section">

            <!-- Title Section -->
            <div class="title-section d-flex align-items-center">
                <h4>Danh sách thuốc</h4>
            </div>

            <!-- Search Section -->
            <div style="width: 465px;">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Theo mã, tên hàng">
                </div>
            </div>
                 
                <div class="ultility-options d-flex align-items-center">
                    <!-- Xuất file -->
                    <button class="btn btn-outline-secondary me-2">
                        <i class="fas fa-upload me-1"></i>
                        Xuất file
                    </button>
                    
                    <!-- Utility Icons -->
                    <div class="utility-icons d-flex align-items-center">
                        <button class="btn" title="Chế độ xem">
                            <i class="fas fa-list"></i>
                        </button>
                        <button class="btn" title="Cài đặt">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="btn" title="Trợ giúp">
                            <i class="fas fa-question-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-container">
            <table class="table table-bordered table-hover align-middle">
                <thead class="custom-header">
                    <tr>
                        <th>Mã thuốc</th>
                        <th>Tên thuốc</th>
                        <th>Hoạt chất chính</th>
                        <th>Hàm lượng</th>
                        <th>Quy cách đóng gói</th>
                        <th>ĐVT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->ma_hang ?? 'N/A' }}</td>
                            <td>{{ $medicine->ten_thuoc ?? 'N/A' }}</td>
                            <td>{{ $medicine->hoat_chat ?? 'N/A' }}</td>
                            <td>{{ $medicine->ham_luong ?? 'N/A' }}</td>
                            <td>{{ $medicine->quy_cach_dong_goi ?? 'N/A' }}</td>
                            <td>{{ $medicine->don_vi_tinh ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Không có thuốc nào được tìm thấy</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            @if($medicines->hasPages())
                <div class="medicine-pagination d-flex justify-content-center mt-4">
                    {{ $medicines->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection