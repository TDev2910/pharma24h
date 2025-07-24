@extends('layouts.user')

@section('title', 'MediAid - Nhà thuốc trực tuyến')

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

    <div class="container mt-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
    <div class="row">
        <!-- Cột Nội Dung -->
        <div class="col-md-6">
            <div class="MuiBox-root css-11mgf69">
                <div class="MuiBox-root css-11icu4z">
                    <br>
                    <br>
                    <br>
                    <p class="MuiTypography-root MuiTypography-body1 css-1vvyywl" texttype="text28S"><h1>Dược sĩ tận tâm, tư vấn chuyên nghiệp</h1></p>
                    <p class="MuiTypography-root MuiTypography-body1 css-1yi16y8" texttype="text14R">Đội ngũ Dược sĩ được đào tạo bài bản, có chuyên môn cao và giàu kinh nghiệm. Luôn đặt lợi ích khách hàng lên hàng đầu, sẵn sàng tư vấn, giải đáp thắc mắc và hướng dẫn sử dụng sản phẩm hiệu quả, an toàn.</p>
                </div>
            </div>
        </div>
        
        <!-- Cột Hình Ảnh -->
        <div class="col-md-6">
            <div class="MuiBox-root css-1g29oey">
                <img loading="lazy" alt="Introduction" src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/introduction/picture_1.webp" width="80%" style="max-height: 80%; object-fit: cover; border-radius: 8px;">
            </div>
        </div>
    </div>
    </div>
    {{-- DỊCH VỤ --}}
    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_1.webp" alt="Cam kết 100%" style="height:60px;">
                        <h5 class="fw-bold mt-3 mb-2 text-primary">CAM KẾT 100%</h5>
                        <p class="mb-0">Thuốc chính hãng, đa dạng và chuyên sâu</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_2.webp" alt="Giao hàng nhanh chóng" style="height:60px;">
                        <h5 class="fw-bold mt-3 mb-2 text-primary">GIAO HÀNG NHANH CHÓNG</h5>
                        <p class="mb-0">Giao hàng tận nhà hoặc nhận tại cửa hàng</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_3.webp" alt="Đổi trả trong 30 ngày" style="height:60px;">
                        <h5 class="fw-bold mt-3 mb-2 text-primary">ĐỔI TRẢ TRONG 30 NGÀY</h5>
                        <p class="mb-0">Kể từ ngày mua hàng và hoàn trả trong tháng</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <img src="https://cdn.kiotvietweb.vn/page_builder_default_config/pharmacy/theme_2/homepage/service/picture_4.webp" alt="Đa dạng sản phẩm" style="height:60px;">
                        <h5 class="fw-bold mt-3 mb-2 text-primary">ĐA DẠNG SẢN PHẨM</h5>
                        <p class="mb-0">Đa dạng các loại thuốc, thực phẩm chức năng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.container-fluid {
    padding-left: 0;
    padding-right: 0;
}

.hero-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    margin: 2rem 0;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush
