@extends('layouts.admin')

@section('title', 'Chỉnh sửa Nhà Cung Cấp')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-edit text-primary me-2"></i>Chỉnh sửa Nhà Cung Cấp
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Nhà Cung Cấp</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.show', $supplier) }}">{{ $supplier->ma_nha_cung_cap }}</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline-info me-2">
                <i class="fas fa-eye me-1"></i>Xem chi tiết
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">
                <i class="fas fa-edit me-2"></i>Cập nhật thông tin: {{ $supplier->ten_nha_cung_cap }}
            </h5>
        </div>
        
        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('admin.products.Nhaphang.Suppliers.partials.supplier-form')
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Hủy
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save me-1"></i>Cập nhật nhà cung cấp
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.products.Nhaphang.Suppliers.partials.supplier-modal-scripts')
@endpush
