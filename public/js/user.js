// Initialize Google Maps
function initGoogleMaps() {
    // Tọa độ của địa chỉ mới (12 Đô Lương, Phường 11, Vũng Tàu, Bà Rịa - Vũng Tàu)
    const mediaidLocation = { lat: 10.3551, lng: 107.0775 };
    
    const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 16,
        center: mediaidLocation,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    const marker = new google.maps.Marker({
        position: mediaidLocation,
        map: map,
        title: "MediAid - Nhà thuốc trực tuyến",
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="#dc3545">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
            `),
            scaledSize: new google.maps.Size(40, 40)
        }
    });

    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; max-width: 250px;">
                <h6 style="color: #1976d2; margin-bottom: 10px;">
                    <i class="fas fa-plus-circle"></i> MediAid
                </h6>
                <p style="margin: 5px 0; font-size: 14px;">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    12 Đô Lương, Phường 11, Vũng Tàu, Bà Rịa - Vũng Tàu
                </p>
                <p style="margin: 5px 0; font-size: 14px;">
                    <i class="fas fa-phone text-success"></i>
                    <a href="tel:0980xxxxxx">0980xxxxxx</a>
                </p>
                <p style="margin: 5px 0; font-size: 14px;">
                    <i class="fas fa-clock text-primary"></i>
                    7:00 - 22:00 (Thứ 2 - CN)
                </p>
            </div>
        `
    });

    marker.addListener("click", () => {
        infoWindow.open(map, marker);
    });
}

// Back to top functionality - optimized
function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 200) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Initialize tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Update DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // User JavaScript loaded successfully
    
    // Initialize all components
    if (typeof initSmoothScrolling === 'function') initSmoothScrolling();
    if (typeof initNavbarScroll === 'function') initNavbarScroll();
    if (typeof initCartFunctionality === 'function') initCartFunctionality();
    if (typeof initSearchFunctionality === 'function') initSearchFunctionality();
    if (typeof initBackToTop === 'function') initBackToTop();
    if (typeof initTooltips === 'function') initTooltips();
    
    // Initialize Google Maps if element exists
    if (document.getElementById('googleMap')) {
        // Load Google Maps API
        if (typeof google === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initGoogleMaps';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
            
            // Fallback nếu không có API key
            window.initGoogleMaps = initGoogleMaps;
        } else {
            initGoogleMaps();
        }
    }
});
