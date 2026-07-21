<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Hospital Universitario del Valle') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: url('{{ asset('images/huv.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            color: #fff;
            position: relative;
        }
        
        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: 1;
        }
        
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }
        
        .transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .transition-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        
        .huge-image-container {
            width: 90vw;
            height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .transition-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0;
            transition: opacity 0.5s ease;
            filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.8));
            animation: none;
        }
        
        .transition-overlay.active .transition-logo {
            opacity: 1;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.8));
            }
            50% {
                transform: scale(1.05);
                filter: drop-shadow(0 0 40px rgba(255, 255, 255, 1));
            }
            100% {
                transform: scale(1);
                filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.8));
            }
        }
        
        .main-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4%;
            max-width: 1600px;
            margin: 0 auto;
            position: relative;
            z-index: 3;
        }
        
        .banner-container {
            width: 100%;
            max-width: 60%;
            height: 70vh;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.7);
            margin-right: 30px;
            flex: 3;
            position: relative;
            z-index: 4;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .auth-card-container {
            width: 100%;
            max-width: 40%;
            flex: 2;
            height: 70vh;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 4;
        }
        
        .swiper {
            width: 100%;
            height: 100%;
        }
        
        .swiper-slide {
            text-align: center;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        
        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease, filter 0.8s ease;
        }
        
        .swiper-slide:nth-child(1) img {
            object-position: center 15%;
            object-fit: cover;
            transform-origin: center 20%;
        }
        
        .swiper-slide:nth-child(2) img {
            object-position: center center;
        }
        
        .swiper-slide:nth-child(3) img {
            object-position: center 30%;
        }
        
        .swiper-slide:nth-child(4) img {
            object-position: center center;
        }
        
        .swiper-slide:nth-child(5) img {
            object-position: center 45%;
        }
        
        .swiper-slide-active img {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
        
        .swiper-slide:nth-child(1).swiper-slide-active img {
            transform: scale(1.05) translateY(-5%);
        }
        
        .swiper-slide-prev img, .swiper-slide-next img {
            transform: scale(0.95);
            filter: brightness(0.9);
        }
        
        .swiper-pagination-bullet {
            background: #fff;
            opacity: 0.7;
            width: 10px;
            height: 10px;
        }
        
        .swiper-pagination-bullet-active {
            background: #004080;
            opacity: 1;
            width: 14px;
            height: 14px;
        }
        
        .image-caption {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background-color: rgba(0, 64, 128, 0.7);
            color: white;
            padding: 12px 15px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 500;
            transform: translateY(100%);
            transition: transform 0.5s ease;
            backdrop-filter: blur(5px);
            border-radius: 10px;
            width: auto;
            max-width: 80%;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        
        .swiper-slide-active .image-caption {
            transform: translateY(0);
        }
        
        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(0deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0) 50%);
            z-index: 1;
            opacity: 0;
            transition: opacity 0.8s ease;
        }
        
        .swiper-slide-active .banner-overlay {
            opacity: 1;
        }
        
        .image-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #004080;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            transform: translateX(100px);
            opacity: 0;
            transition: transform 0.6s ease, opacity 0.6s ease;
            z-index: 2;
        }
        
        .swiper-slide-active .image-badge {
            transform: translateX(0);
            opacity: 1;
            transition-delay: 0.3s;
        }
        
        .animated-slide .image-caption {
            animation: fadeUpIn 0.8s forwards;
        }
        
        .animated-slide .image-badge {
            animation: slideInRight 0.8s forwards;
            animation-delay: 0.3s;
        }
        
        .animated-slide img {
            animation: zoomEnhance 4s forwards;
        }
        
        @keyframes fadeUpIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes zoomEnhance {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.08);
            }
            100% {
                transform: scale(1.05);
            }
        }
        
        .auth-container {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 4;
            backdrop-filter: blur(5px);
        }
        
        .row.g-0 {
            display: flex;
            flex-wrap: wrap;
            margin-right: 0;
            margin-left: 0;
            height: 100%;
        }
        
        .col-md-4.card-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #004080;
            color: white;
            padding: 30px;
            height: 100%;
        }
        
        .col-md-8.card-right {
            display: flex;
            flex-direction: column;
            padding: 30px;
            color: #333;
            height: 100%;
            overflow-y: auto;
        }
        
        .logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
        
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 8px 12px;
        }
        
        .nav-tabs .nav-link.active {
            color: #004080;
            background-color: transparent;
            border-bottom: 3px solid #004080;
        }
        
        .tab-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }
        
        .tab-pane {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            padding-bottom: 20px;
        }
        
        .tab-pane form {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-fields {
            flex: 1;
            min-height: 200px;
            margin-bottom: 30px;
        }
        
        .form-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .empty-space {
            height: 40px;
        }
        
        .tab-pane h3 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #004080;
        }
        
        .form-control {
            border-radius: 5px;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #004080;
            box-shadow: 0 0 0 0.25rem rgba(0, 64, 128, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .btn-primary {
            border-radius: 5px;
            padding: 10px 15px;
            font-weight: 500;
            background-color: #004080;
            border-color: #004080;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #003366;
            border-color: #002b55;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .hospital-info h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .hospital-info p {
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        .features {
            margin-top: 20px;
        }
        
        .feature-item {
            margin-bottom: 12px;
            font-size: 0.9rem;
        }
        
        .feature-item i {
            margin-right: 8px;
            color: #fff;
        }
        
        .hospital-logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            min-height: 150px;
            object-fit: contain;
        }
        
        .logo-container {
            margin-bottom: 25px;
            padding: 10px;
        }
        
        .program-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 20px;
            text-align: center;
            line-height: 1.4;
        }
        
        @media (max-width: 1200px) {
            .main-container {
                flex-direction: column;
                justify-content: center;
                padding: 20px;
                height: auto;
                min-height: 100vh;
            }
            
            .banner-container {
                max-width: 100%;
                height: 45vh;
                margin-right: 0;
                margin-bottom: 20px;
                flex: none;
            }
            
            .auth-card-container {
                max-width: 100%;
                flex: none;
                height: auto;
                min-height: 500px;
            }
            
            .auth-container {
                height: auto;
            }
            
            .auth-card {
                height: auto;
                min-height: 500px;
            }
            
            .card-left, .card-right {
                height: auto;
            }
            
            .hospital-logo {
                min-height: 120px;
                max-width: 80%;
                margin: 0 auto 15px;
            }
            
            .program-title {
                font-size: 1.2rem;
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .huge-image-container {
                width: 100vw;
                height: 80vh;
            }
            
            .transition-logo {
                width: 100%;
                height: 100%;
            }
            
            .banner-container {
                height: 35vh;
            }
            
            .auth-card-container {
                min-height: auto;
            }
            
            .auth-card {
                min-height: auto;
            }
            
            .row.g-0 {
                flex-direction: column;
            }
            
            .col-md-4.card-left {
                width: 100%;
                padding: 20px;
            }
            
            .col-md-8.card-right {
                width: 100%;
            }
            
            .hospital-logo {
                min-height: 100px;
                max-width: 70%;
            }
            
            .program-title {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }
            
            .feature-item {
                margin-bottom: 8px;
                font-size: 0.85rem;
            }
            
            .form-actions {
                position: relative;
                padding-top: 15px;
            }
            
            .empty-space {
                height: 20px;
            }
            
            .transition-logo {
                width: 100% !important;
                max-width: none !important;
            }
            
            .transition-overlay {
                padding: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .main-container {
                padding: 15px;
            }
            
            .banner-container {
                height: 30vh;
                margin-bottom: 15px;
            }
            
            .card-left {
                padding: 15px;
            }
            
            .card-right {
                padding: 15px;
            }
            
            .tab-pane h3 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }
            
            .form-control {
                padding: 8px 10px;
                margin-bottom: 10px;
            }
            
            .btn-primary {
                padding: 8px 12px;
            }
            
            .hospital-logo {
                min-height: 80px;
            }
            
            .program-title {
                font-size: 1rem;
            }
            
            .transition-logo {
                width: 100% !important;
                border-radius: 4px;
            }
            
            .huge-image-container {
                width: 100vw;
                height: 70vh;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="transition-overlay" id="transitionOverlay">
        <div class="huge-image-container">
            <img src="{{ asset('images/Image_Proa.jpeg') }}" alt="PROA Logo" class="transition-logo" id="transitionLogo">
        </div>
    </div>
    <div class="overlay">
        <div class="main-container">
            <!-- Banner de imágenes dinámicas -->
            <div class="banner-container">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="banner-overlay"></div>
                            <div class="image-badge">HUV</div>
                            <img src="{{ asset('images/inicio/imagen1.jpeg') }}"
                                 alt="Hospital Universitario del Valle - Vista exterior del hospital"
                                 loading="lazy"
                                 onerror="this.style.display='none'; console.warn('Error cargando imagen1.jpeg');">
                            <div class="image-caption">Hospital Universitario del Valle</div>
                        </div>
                        <div class="swiper-slide">
                            <div class="banner-overlay"></div>
                            <div class="image-badge">Instalaciones</div>
                            <img src="{{ asset('images/inicio/imagen2.jpeg') }}"
                                 alt="Instalaciones Médicas - Equipos y tecnología médica avanzada"
                                 loading="lazy"
                                 onerror="this.style.display='none'; console.warn('Error cargando imagen2.jpeg');">
                            <div class="image-caption">Instalaciones Médicas</div>
                        </div>
                        <div class="swiper-slide">
                            <div class="banner-overlay"></div>
                            <div class="image-badge">Atención</div>
                            <img src="{{ asset('images/inicio/imagen3.jpeg') }}"
                                 alt="Atención de Calidad - Personal médico brindando atención especializada"
                                 loading="lazy"
                                 onerror="this.style.display='none'; console.warn('Error cargando imagen3.jpeg');">
                            <div class="image-caption">Atención de Calidad</div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <!-- Contenedor de autenticación -->
            <div class="auth-card-container">
                <div class="auth-container">
                    <div class="auth-card">
                        <div class="row g-0">
                            <!-- Lado izquierdo - Información del Hospital -->
                            <div class="col-md-4 card-left">
                                <div class="hospital-info">
                                    <div class="logo-container text-center">
                                        <img src="{{ asset('images/Image_Proa.jpeg') }}" alt="Logo Hospital" class="img-fluid hospital-logo">
                                    </div>
                                    <p class="program-title">Programa de uso Optimizado de Antimicrobianos</p>
                                    
                                    <div class="features">
                                        <div class="feature-item">
                                            <i class="fas fa-user-md"></i> Gestión de Pacientes
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-procedures"></i> Control de Procedimientos
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-clipboard-list"></i> Historial Clínico
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-pills"></i> Administración de Medicamentos
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Lado derecho - Formularios de Autenticación -->
                            <div class="col-md-8 card-right">
                                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $errors->hasAny(['usuario','password','email']) && !$errors->has('email_login') ? '' : 'active' }}" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $errors->hasAny(['usuario','password','email']) && !$errors->has('email_login') ? 'active' : '' }}" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content" id="authTabsContent">
                                    <!-- Formulario de Login -->
                                    <div class="tab-pane fade {{ $errors->hasAny(['usuario','password','email']) && !$errors->has('email_login') ? '' : 'show active' }}" id="login" role="tabpanel" aria-labelledby="login-tab">
                                        <h3 class="mb-3">Iniciar Sesión</h3>
                                        
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            
                                            <div class="form-fields">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Correo Electrónico</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Contraseña</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remember">Recordarme</label>
                                                </div>
                                                
                                                <div class="empty-space"></div>
                                            </div>
                                            
                                            <div class="form-actions">
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                                </div>
                                                
                                                @if (Route::has('password.request'))
                                                    <div class="text-center mt-3">
                                                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                                                            ¿Olvidaste tu contraseña?
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Formulario de Registro -->
                                    <div class="tab-pane fade {{ $errors->hasAny(['usuario','password','email']) && !$errors->has('email_login') ? 'show active' : '' }}" id="register" role="tabpanel" aria-labelledby="register-tab">
                                        <h3 class="mb-3">Crear Cuenta</h3>
                                        
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf
                                            
                                            <div class="form-fields">
                                                <div class="mb-3">
                                                    <label for="usuario" class="form-label">Usuario</label>
                                                    <input type="text" class="form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" value="{{ old('usuario') }}" required autocomplete="username" autofocus>
                                                    @error('usuario')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Correo Electrónico</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="register_email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Contraseña</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="register_password" name="password" required autocomplete="new-password">
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                                </div>
                                            </div>
                                            
                                            <div class="form-actions">
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary">Registrarse</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- Particles JS -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Initialize Swiper and Animation -->
    <script>
        // Elementos de transición
        const transitionOverlay = document.getElementById('transitionOverlay');
        const transitionLogo = document.getElementById('transitionLogo');
        
        // Mostrar animación de transición cuando la página se carga
        window.addEventListener('load', function() {
            // Mostrar overlay con la animación
            transitionOverlay.classList.add('active');
            
            // Ocultar la animación después de 2 segundos (reducido de 4)
            setTimeout(function() {
                transitionOverlay.classList.remove('active');
            }, 2000);
        });
        
        // Mostrar animación de transición cuando se cambia entre pestañas
        const tabLinks = document.querySelectorAll('.nav-link');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Si no es la pestaña activa, mostrar animación
                if (!this.classList.contains('active')) {
                    e.preventDefault();
                    
                    // Mostrar overlay
                    transitionOverlay.classList.add('active');
                    
                    // Guardar referencia al elemento clickeado
                    const clickedTab = this;
                    
                    // Cambiar a la pestaña después de un breve retraso
                    setTimeout(function() {
                        // Activar manualmente la pestaña
                        const tabTrigger = new bootstrap.Tab(clickedTab);
                        tabTrigger.show();
                        
                        // Limpiar errores de validación al cambiar de tab
                        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                        document.querySelectorAll('.alert-danger').forEach(el => el.remove());
                        
                        // Ocultar overlay después de mostrar la pestaña
                        setTimeout(function() {
                            transitionOverlay.classList.remove('active');
                        }, 1200);
                    }, 2000);
                }
            });
        });
        
        // Permitir que los formularios se envíen incluso si el overlay está activo
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                console.log('=== FORMULARIO SUBMIT ===');
                console.log('Action:', this.action);
                console.log('Method:', this.method);
                
                // Validar que todos los campos requeridos tengan valor
                const requiredFields = this.querySelectorAll('[required]');
                let allValid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        console.error('Campo vacío:', field.name, field.id);
                        allValid = false;
                    }
                });
                
                if (!allValid) {
                    console.error('Hay campos requeridos vacíos');
                    // No prevenir submit, dejar que el navegador muestre errores nativos
                }
                
                // Ocultar overlay inmediatamente al enviar cualquier formulario
                transitionOverlay.classList.remove('active');
                
                console.log('Formulario enviándose...');
            });
        });
        
        // Inicializar Swiper
        var swiper = new Swiper(".mySwiper", {
            effect: "fade",
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            loop: true,
            grabCursor: true,
            speed: 1200,
            watchSlidesProgress: true,
            mousewheel: {
                enabled: true,
                sensitivity: 1,
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            fadeEffect: {
                crossFade: true
            },
            // Mejorar accesibilidad
            a11y: {
                enabled: true,
                prevSlideMessage: 'Imagen anterior',
                nextSlideMessage: 'Imagen siguiente',
                firstSlideMessage: 'Esta es la primera imagen',
                lastSlideMessage: 'Esta es la última imagen',
            },
            on: {
                init: function() {
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        activeSlide.classList.add('animated-slide');
                    }
                },
                slideChangeTransitionStart: function() {
                    const slides = this.slides;
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].classList.remove('animated-slide');
                    }
                },
                slideChangeTransitionEnd: function() {
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        activeSlide.classList.add('animated-slide');
                    }
                }
            }
        });
        
        // Inicializar Particles.js
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 120,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": ["#ffffff", "#004080", "#005cb3", "#0077b3", "#003366"]
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                    "polygon": {
                        "nb_sides": 5
                    }
                },
                "opacity": {
                    "value": 0.7,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 0.8,
                        "opacity_min": 0.3,
                        "sync": false
                    }
                },
                "size": {
                    "value": 5,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 3,
                        "size_min": 1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 180,
                    "color": "#004080",
                    "opacity": 0.5,
                    "width": 1.5
                },
                "move": {
                    "enable": true,
                    "speed": 1.2,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": true,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": false,
                        "mode": "bubble"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 400,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 200,
                        "size": 6,
                        "duration": 2,
                        "opacity": 0.8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200,
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true,
            "responsive": [
                {
                    "breakpoint": 768,
                    "options": {
                        "particles": {
                            "number": {
                                "value": 80
                            },
                            "line_linked": {
                                "distance": 150
                            }
                        }
                    }
                },
                {
                    "breakpoint": 480,
                    "options": {
                        "particles": {
                            "number": {
                                "value": 50
                            },
                            "size": {
                                "value": 3
                            },
                            "line_linked": {
                                "enable": false
                            },
                            "move": {
                                "speed": 1
                            }
                        }
                    }
                }
            ]
        });
        
        // Ajustar partículas en función del tamaño de la pantalla
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 480) {
                pJSDom[0].pJS.particles.number.value = 50;
                pJSDom[0].pJS.particles.line_linked.enable = false;
            } else if (window.innerWidth <= 768) {
                pJSDom[0].pJS.particles.number.value = 80;
                pJSDom[0].pJS.particles.line_linked.enable = true;
            } else {
                pJSDom[0].pJS.particles.number.value = 120;
                pJSDom[0].pJS.particles.line_linked.enable = true;
            }
            pJSDom[0].pJS.fn.particlesRefresh();
        });
    </script>
</body>
</html>
