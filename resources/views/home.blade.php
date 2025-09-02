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
</style>
@endpush
