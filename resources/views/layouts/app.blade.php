<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventory System') }}</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- FONT --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')

    {{-- SELECT2 FIX --}}
    <style>
        .select2-container { width: 100% !important; }
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 5px 10px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            display: flex;
            align-items: center;
        }
        .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0 !important;
        }
        .select2-selection__arrow { height: 36px !important; }
    </style>

    {{-- THEME --}}
    <style>
        :root {
            --bni-blue: #0f2a44;
            --bni-blue-dark: #143d66;
            --bni-accent: #1d4ed8;
            --bni-orange: #f97316;
        }

        html, body {
            margin: 0;
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f4f6f9;
            overflow: hidden;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 260px;
            min-width: 260px;
            background: transparent;
            display: flex;
            flex-direction: column;
            z-index: 1030;
            overflow: hidden;
            transition: width .6s cubic-bezier(.4,0,.2,1);
        }

        .sidebar::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                var(--bni-blue),
                var(--bni-blue-dark)
            );
            z-index: -1;
            transition:
                opacity .45s ease,
                transform .6s cubic-bezier(.4,0,.2,1);
        }

        .sidebar.collapsed::before {
            opacity: .95;
            transform: scaleX(.97);
        }

        .sidebar-brand {
            overflow: hidden;
            text-align: center;
            transition: padding .3s ease;
        }

        .sidebar-logo {
            height: 40px;
            max-width: 100%;
            object-fit: contain;
            transition: transform .3s ease, height .3s ease;
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 8px;
            scrollbar-width: none;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .sidebar.collapsed {
            width: 72px;
            min-width: 72px;
        }

        .sidebar.collapsed .sidebar-brand {
            padding: 8px 0;
        }

        .sidebar.collapsed .sidebar-logo {
            height: 32px;
            transform: scale(.85);
        }

        .text {
            white-space: nowrap;
            transition: opacity .2s ease, transform .2s ease;
        }

        .sidebar.collapsed .text {
            opacity: 0;
            transform: translateX(-8px);
            pointer-events: none;
        }

        .sidebar.collapsed .sidebar-scroll {
            overflow: hidden;
            padding-right: 0;
        }

        .sidebar .nav-link {
            position: relative;
            color: #dbeafe;
            font-size: 14px;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition:
                background-color .3s ease,
                padding-left .3s ease,
                color .3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,.12);
            color: #fff;
            padding-left: 18px;
        }

        .sidebar .nav-link.active {
            background-color: var(--bni-accent);
            color: #fff;
            font-weight: 500;
        }

        .sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 4px;
            background-color: var(--bni-orange);
            border-radius: 0 4px 4px 0;
        }

        .icon {
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-title {
            font-size: 11px;
            color: #bfdbfe;
            padding: 12px 16px 6px;
            text-transform: uppercase;
            opacity: .8;
            transition: opacity .2s ease;
        }

        .sidebar.collapsed .sidebar-title {
            opacity: 0;
        }

        .sidebar-profile {
            background-color: rgba(0,0,0,.15);
            position: relative;
        }

        .sidebar:not(.collapsed) .sidebar-profile .dropdown-menu {
            bottom: 100%;
            top: auto;
            margin-bottom: .5rem;
        }

        .sidebar.collapsed .sidebar-profile .dropdown-menu {
            position: fixed !important;
            bottom: 70px;
            left: 16px;
            z-index: 9999;
        }

        /* ================= MAIN ================= */
        main {
            margin-left: 260px;
            height: 100vh;
            overflow-y: auto;
            padding: 24px;
            transition: margin-left .6s cubic-bezier(.4,0,.2,1);
        }

        .sidebar.collapsed + main {
            margin-left: 72px;
        }
    </style>
</head>

<body>

@include('layouts.navigation')

<main>

    {{-- ✅ GLOBAL ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-triangle-exclamation me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-circle-info me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')

</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById('sidebar');
    const scrollArea = sidebar?.querySelector('.sidebar-scroll');
    const toggle = document.getElementById('toggleSidebar');

    if (!sidebar || !scrollArea) return;

    const savedScroll = localStorage.getItem('sidebar-scroll');
    if (savedScroll !== null) {
        scrollArea.scrollTop = parseInt(savedScroll);
    }

    scrollArea.addEventListener('scroll', () => {
        localStorage.setItem('sidebar-scroll', scrollArea.scrollTop);
    });

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });

    // ✅ AUTO HIDE ALERT 3 DETIK
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
});
</script>

</body>
</html>