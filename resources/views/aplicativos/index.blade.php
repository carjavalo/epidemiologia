<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Epidemiología Hospitalaria — Centro de aplicativos</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --navy: #2a377e;
            --navy-2: #212a63;
            --bg: #f4f6fb;
            --borde: #e5e7f0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: #262b34;
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* ---------- Barra superior ---------- */
        .ca-top {
            background: #fff;
            border-bottom: 1px solid var(--borde);
            padding: 14px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .ca-marca { display: flex; align-items: center; gap: 12px; }
        .ca-marca-ico {
            width: 42px; height: 42px; border-radius: 12px;
            background: #eef1fa; color: var(--navy);
            display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
        }
        .ca-marca h1 { font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--navy); }
        .ca-marca p  { margin: 1px 0 0; font-size: .78rem; color: #8b93a5; }

        .ca-user { display: flex; align-items: center; gap: 10px; }
        .ca-user-nom { font-size: .85rem; color: #5a6172; font-weight: 600; }
        .ca-salir {
            background: #fff; border: 1px solid var(--borde); color: #5a6172;
            border-radius: 10px; padding: 8px 14px; font-size: .84rem; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
            transition: background-color .15s ease, color .15s ease;
        }
        .ca-salir:hover { background: #f2f4fb; color: var(--navy); }

        /* ---------- Encabezado ---------- */
        .ca-wrap { max-width: 1180px; margin: 0 auto; padding: 38px 26px 60px; }
        .ca-titulo { text-align: center; margin-bottom: 34px; }
        .ca-titulo h2 {
            font-size: 1.9rem; font-weight: 700; color: #262b34; margin: 0;
            letter-spacing: -0.02em;
        }
        .ca-titulo p { margin: 8px 0 0; color: #6b7280; font-size: .95rem; }

        /* ---------- Grid de aplicativos ---------- */
        .ca-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .ca-card {
            background: #fff;
            border: 1px solid var(--borde);
            border-radius: 16px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            transition: transform .16s ease, border-color .16s ease;
        }
        .ca-card:hover { transform: translateY(-3px); border-color: #c9d1ea; }
        .ca-card-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .ca-card-ico {
            width: 52px; height: 52px; border-radius: 14px; flex: 0 0 auto;
            background: #eef1fa; display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .ca-card-ico img { width: 100%; height: 100%; object-fit: contain; padding: 3px; }
        .ca-card-ico i { color: var(--navy); font-size: 1.3rem; }
        .ca-card h3 { font-size: 1rem; font-weight: 700; margin: 0; color: #262b34; }
        .ca-tag {
            display: inline-block; margin-top: 3px; font-size: .68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .04em;
            background: #e6f6ee; color: #1c7a4d; padding: 2px 8px; border-radius: 999px;
        }
        .ca-desc {
            color: #6b7280; font-size: .87rem; line-height: 1.5; margin: 0 0 18px;
            flex: 1 1 auto;
        }
        .ca-btn {
            display: block; text-align: center; text-decoration: none;
            background: var(--navy); color: #fff; border-radius: 10px;
            padding: 10px 16px; font-weight: 600; font-size: .9rem;
            transition: background-color .15s ease;
        }
        .ca-btn:hover { background: var(--navy-2); color: #fff; }

        /* Tarjeta reservada para futuros aplicativos */
        .ca-card--proximo { background: #fbfcfe; border-style: dashed; }
        .ca-card--proximo .ca-card-ico { background: #f2f3f8; }
        .ca-card--proximo .ca-card-ico i { color: #9aa2b1; }
        .ca-card--proximo h3 { color: #8b93a5; }
        .ca-btn--off { background: #eef0f5; color: #9aa2b1; cursor: default; }
        .ca-btn--off:hover { background: #eef0f5; color: #9aa2b1; }

        .ca-pie { text-align: center; margin-top: 44px; color: #9aa2b1; font-size: .8rem; }

        @media (max-width: 575.98px) {
            .ca-titulo h2 { font-size: 1.5rem; }
            .ca-wrap { padding: 26px 18px 40px; }
        }
    </style>
</head>
<body>

    <div class="ca-top">
        <div class="ca-marca">
            <span class="ca-marca-ico"><i class="fas fa-hospital"></i></span>
            <div>
                <h1>Epidemiología Hospitalaria</h1>
                <p>Hospital Universitario del Valle &quot;Evaristo García&quot; E.S.E</p>
            </div>
        </div>

        <div class="ca-user">
            <span class="ca-user-nom">{{ auth()->user()->usuario ?? auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="ca-salir"><i class="fas fa-power-off"></i> Salir</button>
            </form>
        </div>
    </div>

    <div class="ca-wrap">
        <div class="ca-titulo">
            <h2>Epidemiología Hospitalaria</h2>
            <p>Centro de aplicativos — seleccione el sistema al que desea ingresar.</p>
        </div>

        <div class="ca-grid">

            {{-- Aplicativo MICRO/PROA --}}
            <div class="ca-card">
                <div class="ca-card-top">
                    <span class="ca-card-ico">
                        <img src="{{ asset('images/logo-microproa.png') }}" alt="MICRO/PROA">
                    </span>
                    <div>
                        <h3>MICRO/PROA</h3>
                        <span class="ca-tag">Disponible</span>
                    </div>
                </div>
                <p class="ca-desc">
                    Vigilancia de microbiología y programa de optimización de antimicrobianos:
                    registros, seguimiento de pacientes y conteo de IAAS.
                </p>
                <a href="{{ route('dashboard') }}" class="ca-btn">
                    <i class="fas fa-right-to-bracket"></i> Acceder
                </a>
            </div>

            {{-- Espacio reservado para futuros aplicativos --}}
            <div class="ca-card ca-card--proximo">
                <div class="ca-card-top">
                    <span class="ca-card-ico"><i class="fas fa-plus"></i></span>
                    <div><h3>Próximo aplicativo</h3></div>
                </div>
                <p class="ca-desc">
                    Este espacio queda listo para los siguientes sistemas del servicio de epidemiología.
                </p>
                <span class="ca-btn ca-btn--off">No disponible</span>
            </div>

        </div>

        <p class="ca-pie">Servicio de Epidemiología · Hospital Universitario del Valle</p>
    </div>

</body>
</html>
