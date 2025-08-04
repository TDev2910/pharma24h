@extends('layouts.user')  <!-- Kế thừa từ layout chung, bao gồm header và footer -->

@section('title', 'Cơ sở khám bệnh')

@section('content')
<div class="container mt-4 mt-md-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
    <div class="row align-items-center">
        <!-- Cột Nội Dung -->
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
            <div class="MuiBox-root css-11mgf69">
                <div class="MuiBox-root css-11icu4z">
                    <h1 class="clinic-title mb-3">Chăm sóc sức khỏe tận tâm</h1>
                    <h2 class="doctor-name mb-3">Dược Sĩ Đạt</h2>
                    <p class="clinic-description">Luôn lắng nghe – Luôn đồng hành. Chúng tôi mang đến môi trường khám chữa bệnh thân thiện, an toàn và hiệu quả.</p>
                </div>
            </div>
        </div>
        
        <!-- Cột Hình Ảnh -->
        <div class="col-lg-6 col-md-12">
            <div class="MuiBox-root css-1g29oey text-center text-lg-end">
                <img loading="lazy" alt="Bác sĩ Đạt" src="images/bacsidat.png" 
                     class="doctor-image" 
                     style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
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

/* Clinic Section Styles */
.clinic-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.doctor-name {
    font-size: 2rem;
    font-weight: 600;
    color: #007bff;
    line-height: 1.3;
    margin-bottom: 1rem;
}

.clinic-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #6c757d;
    margin-bottom: 0;
}

.doctor-image {
    transition: transform 0.3s ease;
    max-width: 100%;
    height: auto;
}

.doctor-image:hover {
    transform: scale(1.02);
}

/* Mobile Adjustments */
@media (max-width: 768px) {
    .clinic-title {
        font-size: 1.8rem;
        text-align: center;
    }
    
    .doctor-name {
        font-size: 1.5rem;
        text-align: center;
    }
    
    .clinic-description {
        font-size: 1rem;
        text-align: center;
    }
    
    .doctor-image {
        max-width: 90% !important;
        margin: 0 auto;
    }
}

/* iPhone 12 Specific */
@media (max-width: 390px) {
    .clinic-title {
        font-size: 1.5rem;
    }
    
    .doctor-name {
        font-size: 1.3rem;
    }
    
    .clinic-description {
        font-size: 0.9rem;
    }
    
    .doctor-image {
        max-width: 85% !important;
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

.clinic-title,
.doctor-name,
.clinic-description {
    animation: fadeInUp 0.6s ease-out;
}

.doctor-image {
    animation: fadeInUp 0.8s ease-out;
}

/* Enhanced Image Styles */
.doctor-image {
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.doctor-image:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

/* Responsive Image Container */
@media (max-width: 768px) {
    .MuiBox-root.css-1g29oey {
        margin-top: 1rem;
    }
}

/* Text Enhancement */
.clinic-title {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.doctor-name {
    color: #007bff;
    position: relative;
}

.doctor-name::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 2px;
}

@media (max-width: 768px) {
    .doctor-name::after {
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>
@endpush
