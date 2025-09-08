@extends('layouts.home')

@section('title', 'Sức Khỏe 24h - Nhà thuốc trực tuyến')

@section('content')
    {{-- Banner Carousel --}}
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000" style="margin-top: 0;">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0"
                class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"
                aria-label="Slide 2"></button>  
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://cdn.nhathuocbewell.com/images/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBcS9pIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--918088de75b0611a6f53fdca34cb6dea5552095e/1000x0/filters:quality(90)/Banner-web_Hero-Chinh_3708x1240px.jpg" 
                     class="d-block w-100 banner-image" 
                     alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="https://inhopgiaygiare.vn/wp-content/uploads/2024/12/banner-Trai-Nghiem.jpg" 
                     class="d-block w-100 banner-image" 
                     alt="Banner 2">
            </div>
        </div>   
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container mt-4 mt-md-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
        <div class="row align-items-center">
            <!-- Cột Nội Dung -->
            <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                <div class="MuiBox-root css-11mgf69">
                    <div class="MuiBox-root css-11icu4z">
                        <h1 class="hero-title mb-3">Dược sĩ tận tâm, tư vấn chuyên nghiệp</h1>
                        <p class="hero-description">Đội ngũ Dược sĩ được đào tạo bài bản, có chuyên môn cao và giàu kinh nghiệm. Luôn đặt lợi ích khách hàng lên hàng đầu, sẵn sàng tư vấn, giải đáp thắc mắc và hướng dẫn sử dụng sản phẩm hiệu quả, an toàn.</p>
                    </div>
                </div>
            </div>
            
            <!-- Cột Hình Ảnh -->
            <div class="col-lg-6 col-md-12">
                <div class="MuiBox-root css-1g29oey text-center text-lg-end">
                    <img loading="lazy" alt="Introduction" src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/introduction/picture_1.webp" 
                        class="hero-image" 
                        style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                </div>
            </div>
        </div>
    </div>
    
    {{-- SẢN PHẨM MỚI NHẤT --}}
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title">Sản phẩm mới nhất</h2>
                    <p class="section-subtitle">Khám phá những sản phẩm chăm sóc sức khỏe mới nhất được cập nhật hàng ngày</p>
                    <div class="title-divider"></div>
                </div>
            </div>
        </div>
        
        {{-- hàng 1: lấy 4 sản phẩm thuộc danh mục thuốc  --}}
        @if($medicines->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="product-section-title">
                        <i></i>
                        Thuốc mới nhất
                    </h4>
                </div>
            </div>
            <div class="row g-4 mb-5">
                @foreach($medicines as $medicine)
                    <div class="col-6 col-md-6 col-lg-3">
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img src="{{ $medicine->image_url }}" 
                                     alt="{{ $medicine->ten_thuoc }}" 
                                     class="product-image">

                                {{-- Badge: Thuốc --}}
                                <div class="product-badge">
                                    <span class="badge-text medicine-badge">Thuốc</span>
                                </div>

                                {{-- Button: Hover Overplay View --}}
                                <div class="product-overlay">
                                    <button class="btn-quick-view">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-add-cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category">
                                    {{ $medicine->category->name ?? 'Thuốc dị ứng' }}
                                </div>
                                <h5 class="product-name">
                                    {{ Str::limit($medicine->ten_thuoc, 40) }}
                                </h5>
                                <div class="product-manufacturer">
                                    <i class="fas fa-building"></i>
                                    {{ $medicine->manufacturer->name ?? 'Chưa rõ' }}
                                </div>
                                <div class="product-pricing">
                                    <span class="current-price">{{ $medicine->gia_ban_formatted ?? '0 VND' }}</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-primary-action add-to-cart" 
                                            data-item-id="{{ $medicine->id }}" 
                                            data-item-type="medicine">
                                        <i class="fas fa-cart-plus me-1"></i>
                                        Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

         {{-- hàng 1: lấy 4 sản phẩm thuộc danh mục hàng hóa  --}}
        @if($goods->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="product-section-title">
                        <i></i>
                        Hàng hóa mới nhất
                    </h4>
                </div>
            </div>
            <div class="row g-4">
                @foreach($goods as $good)
                    <div class="col-6 col-md-6 col-lg-3">
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <img src="{{ $good->image_url }}" 
                                     alt="{{ $good->ten_hang_hoa }}" 
                                     class="product-image">
                                <div class="product-badge">
                                    <span class="badge-text goods-badge">Hàng hóa</span>
                                </div>
                                <div class="product-overlay">
                                    <button class="btn-quick-view">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-add-cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category">
                                    {{ $good->category->name ?? 'Dưỡng da mặt' }}
                                </div>
                                <h5 class="product-name">
                                    {{ Str::limit($good->ten_hang_hoa, 40) }}
                                </h5>
                                <div class="product-manufacturer">
                                    <i class="fas fa-building"></i>
                                    {{ $good->manufacturer->name ?? 'Chưa rõ' }}
                                </div>
                                <div class="product-pricing">
                                    <span class="current-price">{{ $good->gia_ban_formatted ?? '0 VND' }}</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-primary-action add-to-cart" 
                                            data-item-id="{{ $good->id }}" 
                                            data-item-type="goods">
                                        <i class="fas fa-cart-plus me-1"></i>
                                        Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- EMPTY STATE --}}
        @if($medicines->count() == 0 && $goods->count() == 0)
            <div class="row">
                <div class="col-12">
                    <div class="empty-products text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Chưa có sản phẩm nào</h4>
                        <p class="text-muted">Các sản phẩm mới sẽ được cập nhật sớm</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('products') }}" class="btn-view-all">
                    Xem tất cả sản phẩm
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
    
    {{-- DỊCH VỤ --}}
    <div class="container mt-4 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row text-center g-3 g-md-4">
                    <div class="col-6 col-md-3">
                        <div class="service-card">
                            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_1.webp" alt="Cam kết 100%" class="service-icon">
                            <h5 class="service-title">CAM KẾT 100%</h5>
                            <p class="service-description">Thuốc chính hãng, đa dạng và chuyên sâu</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="service-card">
                            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_2.webp" alt="Giao hàng nhanh chóng" class="service-icon">
                            <h5 class="service-title">GIAO HÀNG NHANH CHÓNG</h5>
                            <p class="service-description">Giao hàng tận nhà hoặc nhận tại cửa hàng</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="service-card">
                            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_3.webp" alt="Đổi trả trong 30 ngày" class="service-icon">
                            <h5 class="service-title">ĐỔI TRẢ TRONG 30 NGÀY</h5>
                            <p class="service-description">Kể từ ngày mua hàng và hoàn trả trong tháng</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="service-card">
                            <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_4.webp" alt="Đa dạng sản phẩm" class="service-icon">
                            <h5 class="service-title">ĐA DẠNG SẢN PHẨM</h5>
                            <p class="service-description">Đa dạng các loại thuốc, thực phẩm chức năng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Reset và Base Styles */
* {
    box-sizing: border-box;
}

.container-fluid {
    padding-left: 0;
    padding-right: 0;
}

/* Banner Carousel Responsive */
#bannerCarousel {
    margin-top: 0;
    overflow: hidden;
}

.banner-image {
    width: 100%;
    height: auto;
    object-fit: cover;
    min-height: 200px;
}

/* Mobile Banner Height */
@media (max-width: 768px) {
    #bannerCarousel {
        margin-top: 0;
    }
    
    .banner-image {
        min-height: 180px;
        max-height: 250px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
        background: rgba(0,0,0,0.3);
        border-radius: 50%;
        margin: 0 10px;
    }
}

/* iPhone 12 Specific */
@media (max-width: 390px) {
    .banner-image {
        min-height: 160px;
        max-height: 200px;
    }
}

/* Hero Section */
.hero-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.2;
}

.hero-description {
    font-size: 1rem;
    line-height: 1.6;
    color: #6c757d;
    margin-bottom: 0;
}

.hero-image {
    transition: transform 0.3s ease;
}

.hero-image:hover {
    transform: scale(1.02);
}

/* Mobile Hero Adjustments */
@media (max-width: 768px) {
    .hero-title {
        font-size: 1.5rem;
        text-align: center;
    }
    
    .hero-description {
        font-size: 0.9rem;
        text-align: center;
    }
    
    .hero-image {
        max-width: 90% !important;
        margin: 0 auto;
    }
}

/* Service Cards */
.service-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    height: 100%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f8f9fa;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.service-icon {
    height: 50px;
    width: auto;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.service-card:hover .service-icon {
    transform: scale(1.1);
}

.service-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #007bff;
    margin: 0.5rem 0;
    line-height: 1.3;
}

.service-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.4;
}

/* Mobile Service Cards */
@media (max-width: 768px) {
    .service-card {
        padding: 1rem 0.5rem;
        margin-bottom: 1rem;
    }
    
    .service-icon {
        height: 40px;
    }
    
    .service-title {
        font-size: 0.8rem;
    }
    
    .service-description {
        font-size: 0.75rem;
    }
}

/* iPhone 12 Specific Service Cards */
@media (max-width: 390px) {
    .service-card {
        padding: 0.75rem 0.5rem;
    }
    
    .service-icon {
        height: 35px;
    }
    
    .service-title {
        font-size: 0.75rem;
    }
    
    .service-description {
        font-size: 0.7rem;
    }
}

/* Container Spacing */
@media (max-width: 768px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    
    .mt-md-5 {
        margin-top: 2rem !important;
    }
}

/* General Card Styles */
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Loading Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.service-card {
    animation: fadeInUp 0.6s ease-out;
}

.service-card:nth-child(1) { animation-delay: 0.1s; }
.service-card:nth-child(2) { animation-delay: 0.2s; }
.service-card:nth-child(3) { animation-delay: 0.3s; }
.service-card:nth-child(4) { animation-delay: 0.4s; }

/* ===== PRODUCT SECTION STYLES (Tailwind-inspired) ===== */

/* Section Header */
.section-header {
    position: relative;
    padding-bottom: 2rem;
}

/* Product Section Titles */
.product-section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid #f1f5f9;
    position: relative;
}

.product-section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 2px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto 1.5rem;
    line-height: 1.6;
}

.title-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    margin: 0 auto;
    border-radius: 2px;
}

/* Product Cards */
.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f1f5f9;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border-color: #e2e8f0;
}

/* Product Image */
.product-image-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8fafc;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

/* Product Badge */
.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 10;
}

.badge-text {
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.medicine-badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.goods-badge {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

/* css hiệu ứng con mắt xem chi tiết sản phẩm */
.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

/*Khi hover vào card → hiển thị overlay */
.product-card:hover .product-overlay {
    opacity: 1;
}

/*Hover vào nút va đổi màu và phóng to */
.btn-quick-view,
.btn-add-cart {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: white;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-quick-view:hover,
.btn-add-cart:hover {
    background: #3b82f6;
    color: white;
    transform: scale(1.1);
}

/* Product Info */
.product-info {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 0.75rem;
    font-weight: 600;
    color: #3b82f6;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.product-name {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    min-height: 2.8rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-manufacturer {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.product-manufacturer i {
    color: #94a3b8;
}

/* Product Pricing */
.product-pricing {
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.current-price {
    font-size: 1.25rem;
    font-weight: 800;
    color: #dc2626;
}

.original-price {
    font-size: 1rem;
    font-weight: 500;
    color: #94a3b8;
    text-decoration: line-through;
}

/* Product Actions */
.product-actions {
    margin-top: auto;
}

.btn-primary-action {
    width: 100%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-primary-action:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
}

/* Empty State */
.empty-products {
    background: #f8fafc;
    border-radius: 16px;
    border: 2px dashed #cbd5e1;
}

/* View All Button */
.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #3b82f6;
    text-decoration: none;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-view-all:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
    }
    
    .section-subtitle {
        font-size: 1rem;
    }
    
    .product-image-wrapper {
        height: 160px;
    }
    
    .product-info {
        padding: 1rem;
    }
    
    .product-name {
        font-size: 0.9rem;
        min-height: 2.4rem;
    }
    
    .current-price {
        font-size: 1.1rem;
    }
    
    .btn-primary-action {
        padding: 10px 16px;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .product-image-wrapper {
        height: 140px;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .btn-view-all {
        padding: 12px 24px;
        font-size: 0.9rem;
    }
}

/* Animation for products */
.product-card {
    animation: fadeInUp 0.6s ease-out;
}

.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
.product-card:nth-child(4) { animation-delay: 0.4s; }
.product-card:nth-child(5) { animation-delay: 0.5s; }
.product-card:nth-child(6) { animation-delay: 0.6s; }
.product-card:nth-child(7) { animation-delay: 0.7s; }
.product-card:nth-child(8) { animation-delay: 0.8s; }
</style>
@endpush
