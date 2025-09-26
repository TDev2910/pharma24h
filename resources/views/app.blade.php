<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @inertiaHead

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- App CSS (giữ CSS hiện có) -->
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">

    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/home-sections.css') }}">
</head>
<body>
    {{-- Vue Header Component --}}
    <div id="header-app"></div>

    {{-- Inertia page content --}}
    <main class="main-content">
        @inertia
    </main>

    {{-- Vue Footer Component --}}
    <div id="footer-app"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App JS hiện có -->
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/user.js') }}"></script>
</body>
</html>


