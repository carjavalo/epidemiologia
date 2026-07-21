<!DOCTYPE html>
<html lang="es" translate="no">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google" content="notranslate">
        <meta http-equiv="Content-Language" content="es">

        <title>@yield('title', 'PROAHUV')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        <style>
            /* Prevenir traducción automática para toda la aplicación */
            body {
                -webkit-translate: 'no';
                translate: 'no';
            }
            
            .notranslate {
                -webkit-translate: 'no';
                translate: 'no';
            }
        </style>
        
        @yield('css')
    </head>
    <body class="font-sans antialiased notranslate">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- Scripts -->
        <script>
            // Prevenir la traducción automática
            if (typeof document.addEventListener === 'function') {
                document.addEventListener('DOMContentLoaded', function() {
                    // Detectar y detener intentos de traducción de Google
                    if (window.google && window.google.translate) {
                        window.google.translate.TranslateElement = function() { return null; };
                    }
                    
                    // Configurar todos los elementos importantes como no traducibles
                    var elements = document.querySelectorAll('table, .card-header, .btn, label, input, select, textarea');
                    for (var i = 0; i < elements.length; i++) {
                        elements[i].classList.add('notranslate');
                    }
                });
            }
        </script>
        
        @yield('js')
    </body>
</html>
