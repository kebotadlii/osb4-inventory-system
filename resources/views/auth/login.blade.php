<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventory Management System</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bni-blue: #0f2a44;
            --bni-blue-mid: #143d66;
            --bni-blue-light: #1f4e79;
            --bni-orange: #f97316;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;

            background:
                /* soft highlight kiri atas */
                radial-gradient(
                    1200px circle at 15% 20%,
                    rgba(31,78,121,0.55),
                    transparent 60%
                ),

                /* soft shadow kanan bawah */
                radial-gradient(
                    1000px circle at 85% 80%,
                    rgba(15,42,68,0.85),
                    transparent 65%
                ),

                /* main gradient */
                linear-gradient(
                    135deg,
                    var(--bni-blue) 0%,
                    var(--bni-blue-mid) 50%,
                    var(--bni-blue-light) 100%
                );
        }

        /* SOFT NOISE */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='120' height='120' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* CENTER */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        /* GLOW BEHIND CARD */
        .login-wrapper::before {
            content: "";
            position: absolute;
            width: 520px;
            height: 520px;
            background: radial-gradient(
                circle,
                rgba(249,115,22,0.18),
                transparent 65%
            );
            filter: blur(90px);
            z-index: 0;
        }

        /* CARD */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 18px;
            background-color: #ffffff;
            box-shadow: 0 30px 60px rgba(0,0,0,.35);
            animation: fadeUp .6s ease-out;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 40px 70px rgba(0,0,0,.4);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .login-header img {
            height: 46px;
            margin-bottom: 10px;
        }

        .login-header h5 {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .login-header small {
            color: #6c757d;
        }

        /* INPUT */
        .form-control {
            padding-left: 42px;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .form-control:focus {
            border-color: var(--bni-orange);
            box-shadow: 0 0 0 .2rem rgba(249,115,22,.25);
        }

        /* BUTTON */
        .btn-bni {
            background-color: var(--bni-orange);
            border: none;
        }

        .btn-bni:hover {
            background-color: #ea580c;
        }

        /* FOOTER */
        .login-footer {
            position: fixed;
            bottom: 14px;
            right: 18px;
            font-size: 12px;
            color: rgba(255,255,255,.75);
            pointer-events: none;
            z-index: 2;
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="card login-card">
        <div class="card-body p-4">

            {{-- HEADER --}}
            <div class="login-header">
                <img src="{{ asset('assets/logo/BNI_logo.png') }}" alt="BNI Logo">
                <h5>Inventory Management System</h5>
                <small>BNI Corporate University – OSB4</small>
            </div>

            {{-- SESSION STATUS --}}
            @if (session('status'))
                <div class="alert alert-success small">
                    {{ session('status') }}
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- EMAIL --}}
                <div class="mb-3 position-relative">
                    <i class="bi bi-envelope input-icon"></i>
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="mb-3 position-relative">
                    <i class="bi bi-lock input-icon"></i>
                    <input
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- REMEMBER --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">
                            Remember me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none">
                            Lupa password?
                        </a>
                    @endif
                </div>

                {{-- BUTTON --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-bni text-white fw-semibold py-2">
                        Login
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<div class="login-footer">
    Internal System • Authorized Personnel Only
</div>

</body>
</html>
