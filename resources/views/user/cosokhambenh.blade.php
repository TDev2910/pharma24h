@extends('layouts.user')  <!-- Kế thừa từ layout chung, bao gồm header và footer -->

@section('title', 'Cơ sở khám bệnh')

@section('content')
<div class="container mt-5" id="section_introduction3" data-section-name="Introduction3" data-section-active="true">
        <div class="row">
            <!-- Cột Nội Dung -->
            <div class="col-md-6">
                <div class="MuiBox-root css-11mgf69">
                    <div class="MuiBox-root css-11icu4z">
                        <br>
                        <br>
                        <br>
                        <p class="MuiTypography-root MuiTypography-body1 css-1vvyywl" texttype="text28S"><h1>Chăm sóc sức khỏe tận tâm</h1></p>
                        <p class="MuiTypography-root MuiTypography-body1 css-1vvyywl" texttype="text28S"><h2>Dược Sĩ Đạt </h2></p>
                        <p class="MuiTypography-root MuiTypography-body1 css-1yi16y8" texttype="text14R">Luôn lắng nghe – Luôn đồng hành. Chúng tôi mang đến môi trường khám chữa bệnh thân thiện, an toàn và hiệu quả.</p>
                    </div>
                </div>
            </div>
            
            <!-- Cột Hình Ảnh -->
            <div class="col-md-6">
                <div class="MuiBox-root css-1g29oey">
                    <img loading="lazy" alt="Introduction" src="images/bacsidat.png" width="80%" style="max-height: 80%; object-fit: cover; border-radius: 8px; margin-left: 10%;">
                </div>
            </div>
        </div>
    </div>   
@endsection
