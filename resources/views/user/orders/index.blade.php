@extends('layouts.user')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Đơn hàng của tôi</h2>
                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5>Chưa có đơn hàng nào</h5>
                    <p class="text-muted">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm!</p>
                    <a href="{{ route('products') }}" class="btn btn-primary">Mua sắm ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
