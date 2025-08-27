@extends('layouts.admin')

@section('title', 'Chi tiết Nhà Cung Cấp')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-eye text-primary me-2"></i>Chi tiết Nhà Cung Cấp
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Nhà Cung Cấp</a></li>
                    <li class="breadcrumb-item active">{{ $supplier->ma_nha_cung_cap }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Chỉnh sửa
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Supplier Info Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-building me-2"></i>{{ $supplier->ten_nha_cung_cap }}
                <span class="badge bg-light text-dark ms-2">{{ $supplier->ma_nha_cung_cap }}</span>
            </h5>
        </div>
        
        <div class="card-body">
            <div class="row">
                <!-- Thông tin cơ bản -->
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                    </h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 140px;">Mã NCC:</td>
                            <td>{{ $supplier->ma_nha_cung_cap }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tên NCC:</td>
                            <td>{{ $supplier->ten_nha_cung_cap }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nhóm:</td>
                            <td>
                                @if($supplier->category)
                                    <span class="badge bg-info">{{ $supplier->category->name }}</span>
                                @else
                                    <span class="text-muted">Chưa phân nhóm</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Trạng thái:</td>
                            <td>{!! $supplier->trang_thai_badge !!}</td>
                        </tr>
                    </table>
                </div>

                <!-- Thông tin liên hệ -->
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-phone me-2"></i>Thông tin liên hệ
                    </h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 140px;">Điện thoại:</td>
                            <td>
                                <a href="tel:{{ $supplier->dien_thoai }}" class="text-decoration-none">
                                    {{ $supplier->dien_thoai }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td>
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                        {{ $supplier->email }}
                                    </a>
                                @else
                                    <span class="text-muted">Chưa có</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Địa chỉ:</td>
                            <td>{{ $supplier->dia_chi_day_du }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($supplier->ten_cong_ty || $supplier->ma_so_thue || $supplier->ghi_chu)
            <hr>
            <div class="row">
                <!-- Thông tin công ty -->
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-building me-2"></i>Thông tin công ty
                    </h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 140px;">Tên công ty:</td>
                            <td>{{ $supplier->ten_cong_ty ?: 'Chưa có' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Mã số thuế:</td>
                            <td>{{ $supplier->ma_so_thue ?: 'Chưa có' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Ghi chú -->
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-sticky-note me-2"></i>Ghi chú
                    </h6>
                    <p class="text-muted">
                        {{ $supplier->ghi_chu ?: 'Không có ghi chú' }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        <div class="card-footer text-muted">
            <div class="row">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-calendar-plus me-1"></i>
                        Tạo: {{ $supplier->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small>
                        <i class="fas fa-calendar-edit me-1"></i>
                        Cập nhật: {{ $supplier->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
