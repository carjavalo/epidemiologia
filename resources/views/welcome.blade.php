<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'ProAHUV') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --pa-navy: #2a377e;
            --pa-navy-2: #212a63;
            --pa-accent: #2f6fed;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #F4F6FB;
            color: #333;
        }

        .auth-card {
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 18px;
            box-shadow: none;
            border: 1px solid #e5e7f0;
            overflow: hidden;
        }
        .auth-row { display: flex; flex-wrap: wrap; }

        /* Panel izquierdo (marca) */
        .brand-side {
            flex: 0 0 38%;
            max-width: 38%;
            background: var(--pa-navy);
            color: #fff;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .brand-logo {
            width: 92px; height: 92px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex; align-items: center; justify-content: center;
            padding: 6px; margin-bottom: 18px; overflow: hidden;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.28);
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .brand-name { font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: .01em; }
        .brand-name span { opacity: .85; font-weight: 400; }
        .brand-desc { margin: 8px 0 24px; font-size: .9rem; color: rgba(255, 255, 255, 0.82); line-height: 1.5; }
        .brand-features { list-style: none; padding: 0; margin: 0; }
        .brand-features li {
            display: flex; align-items: center; gap: 10px;
            font-size: .88rem; color: rgba(255, 255, 255, 0.9);
            padding: 7px 0;
        }
        .brand-features i { width: 20px; color: rgba(255, 255, 255, 0.75); }

        /* Panel derecho (formularios) */
        .form-side { flex: 1 1 0; padding: 34px 36px; min-width: 300px; }

        .nav-tabs { border-bottom: 1px solid #e7e9ef; margin-bottom: 22px; }
        .nav-tabs .nav-link {
            border: none; color: #8a93a6; font-weight: 600; font-size: .95rem;
            padding: 8px 4px; margin-right: 24px; background: none;
        }
        .nav-tabs .nav-link.active {
            color: var(--pa-navy); background: none;
            border-bottom: 3px solid var(--pa-navy);
        }
        .form-side h3 { font-size: 1.15rem; font-weight: 700; color: #262b34; margin-bottom: 18px; }
        .form-label { font-size: .82rem; font-weight: 600; color: #5a6172; margin-bottom: 5px; }
        .form-control {
            border: 1px solid #dfe2ec; border-radius: 10px; padding: 10px 12px; font-size: .92rem;
        }
        .form-control:focus { border-color: var(--pa-accent); box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12); }
        .btn-primary {
            background: var(--pa-navy); border: none; border-radius: 10px;
            padding: 11px; font-weight: 600; font-size: .95rem;
        }
        .btn-primary:hover { background: var(--pa-navy-2); }
        .form-check-label { font-size: .88rem; color: #5a6172; }

        @media (max-width: 767.98px) {
            .brand-side { flex: 0 0 100%; max-width: 100%; padding: 30px 28px; }
            .form-side { padding: 28px; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-row">
            <!-- Marca con el logo institucional MICRO/PROA -->
            <div class="brand-side">
                <div class="brand-logo"><img src="{{ asset('images/logo-microproa.png') }}" alt="Logo MICRO/PROA"></div>
                <h1 class="brand-name">MICRO<span>/PROA</span></h1>
                <p class="brand-desc">Programa de Uso Optimizado de Antimicrobianos — Hospital Universitario del Valle.</p>
                <ul class="brand-features">
                    <li><i class="fas fa-user-md"></i> Gestión de pacientes</li>
                    <li><i class="fas fa-procedures"></i> Control de procedimientos</li>
                    <li><i class="fas fa-clipboard-list"></i> Historial clínico</li>
                    <li><i class="fas fa-pills"></i> Administración de medicamentos</li>
                </ul>
            </div>

            <!-- Formularios -->
            <div class="form-side">
                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $errors->registro->any() ? '' : 'active' }}" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $errors->registro->any() ? 'active' : '' }}" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
                    </li>
                </ul>

                <div class="tab-content" id="authTabsContent">
                    <!-- Formulario de Login -->
                    <div class="tab-pane fade {{ $errors->registro->any() ? '' : 'show active' }}" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <h3>Iniciar Sesión</h3>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="login_email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="login_email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                            {{-- El acceso es por usuario (sin correo); el restablecimiento
                                 de contraseña lo hace un administrador desde Configuración › Usuarios. --}}
                        </form>
                    </div>

                    <!-- Formulario de Registro -->
                    <div class="tab-pane fade {{ $errors->registro->any() ? 'show active' : '' }}" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <h3>Crear Cuenta</h3>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="register_usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control @error('usuario', 'registro') is-invalid @enderror" id="register_usuario" name="usuario" value="{{ old('usuario') }}" required autocomplete="username">
                                @error('usuario', 'registro')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="register_email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control @error('email', 'registro') is-invalid @enderror" id="register_email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email', 'registro')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="register_password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control @error('password', 'registro') is-invalid @enderror" id="register_password" name="password" required autocomplete="new-password">
                                @error('password', 'registro')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
