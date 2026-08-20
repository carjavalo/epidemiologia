@extends('adminlte::page')

@section('title', 'Importar seguimiento microbiológico')

@section('content_top_nav_right')
    @include('partials.notificaciones-bell')
@stop

@section('content_header')
    <div class="imp-head">
        <h1 class="imp-title"><i class="fas fa-file-import mr-2"></i>Importar seguimiento microbiológico</h1>
        <p class="imp-sub">Carga los archivos de epidemiología y PROA. Se aceptan .xlsx, .ods, .csv y .txt</p>
    </div>
@stop

@section('content')

    @php
        $avisos     = session('avisos_importacion', []);
        $resEpi     = session('resultado_epidemiologia');
        $resProa    = session('resultado_proa');
        $hayResumen = $resEpi || $resProa;
    @endphp

    <div class="imp-bento">

        {{-- ── Mensajes de estado ───────────────────────────────────────── --}}
        @if(session('success'))
            <div class="imp-cell imp-cell--full">
                <div class="imp-flash imp-flash--ok">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="imp-cell imp-cell--full">
                <div class="imp-flash imp-flash--err">
                    <i class="fas fa-times-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="imp-cell imp-cell--full">
                <div class="imp-flash imp-flash--err">
                    <i class="fas fa-times-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Resumen de la última importación ─────────────────────────── --}}
        @if($hayResumen)
            <div class="imp-cell imp-cell--half">
                <div class="imp-card imp-card--epi">
                    <div class="imp-card-head">
                        <span class="imp-ico"><i class="fas fa-vial"></i></span>
                        <h3>Epidemiología</h3>
                    </div>
                    <div class="imp-stats">
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_epidemiologia.procesados', 0) }}</span>
                            <span class="imp-stat-lbl">Procesados</span>
                        </div>
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_epidemiologia.pacientesCreados', 0) }}</span>
                            <span class="imp-stat-lbl">Pacientes nuevos</span>
                        </div>
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_epidemiologia.seguimientosCreados', 0) }}</span>
                            <span class="imp-stat-lbl">Seguimientos</span>
                        </div>
                        <div class="imp-stat {{ session('resultado_epidemiologia.omitidos', 0) > 0 ? 'is-warn' : '' }}">
                            <span class="imp-stat-num">{{ session('resultado_epidemiologia.omitidos', 0) }}</span>
                            <span class="imp-stat-lbl">Omitidos</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="imp-cell imp-cell--half">
                <div class="imp-card imp-card--proa">
                    <div class="imp-card-head">
                        <span class="imp-ico"><i class="fas fa-capsules"></i></span>
                        <h3>PROA</h3>
                    </div>
                    <div class="imp-stats">
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_proa.procesados', 0) }}</span>
                            <span class="imp-stat-lbl">Procesados</span>
                        </div>
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_proa.actualizados', 0) }}</span>
                            <span class="imp-stat-lbl">Actualizados</span>
                        </div>
                        <div class="imp-stat">
                            <span class="imp-stat-num">{{ session('resultado_proa.sinPaciente', 0) }}</span>
                            <span class="imp-stat-lbl">Sin paciente</span>
                        </div>
                        <div class="imp-stat {{ session('resultado_proa.omitidos', 0) > 0 ? 'is-warn' : '' }}">
                            <span class="imp-stat-num">{{ session('resultado_proa.omitidos', 0) }}</span>
                            <span class="imp-stat-lbl">Omitidos</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Detalle de filas con problemas ───────────────────────────── --}}
        @if(!empty($avisos))
            <div class="imp-cell imp-cell--full">
                <div class="imp-card imp-card--warn">
                    <div class="imp-card-head">
                        <span class="imp-ico imp-ico--warn"><i class="fas fa-triangle-exclamation"></i></span>
                        <div>
                            <h3>{{ count($avisos) }} fila(s) necesitan revisión</h3>
                            <p class="imp-card-sub">
                                El resto se importó con normalidad. Cada línea indica la fila, el paciente
                                y la columna del archivo que hay que corregir.
                            </p>
                        </div>
                    </div>

                    <div class="imp-avisos">
                        @foreach(array_slice($avisos, 0, 300) as $aviso)
                            <div class="imp-aviso">{{ $aviso }}</div>
                        @endforeach
                    </div>

                    @if(count($avisos) > 300)
                        <p class="imp-card-sub mt-2 mb-0">
                            Se muestran las primeras 300 de {{ count($avisos) }}.
                            El detalle completo queda en <code>storage/logs/laravel.log</code>.
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── Formulario de carga ──────────────────────────────────────── --}}
        <div class="imp-cell imp-cell--full">
            <div class="imp-card">
                <div class="imp-card-head">
                    <span class="imp-ico"><i class="fas fa-upload"></i></span>
                    <h3>Cargar archivos</h3>
                </div>

                <form action="{{ route('seguimiento.importar.procesar') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="imp-fields">
                        <div class="imp-field">
                            <label for="archivo_epidemiologia">
                                Epidemiología <span class="imp-req">obligatorio</span>
                            </label>
                            <input type="file" id="archivo_epidemiologia" name="archivo_epidemiologia"
                                   class="form-control" accept=".txt,.csv,.xlsx,.ods" required>
                            <small>Base madre de seguimiento microbiológico.</small>
                        </div>

                        <div class="imp-field">
                            <label for="archivo_proa">
                                PROA / procedimientos <span class="imp-opt">opcional</span>
                            </label>
                            <input type="file" id="archivo_proa" name="archivo_proa"
                                   class="form-control" accept=".txt,.csv,.xlsx,.ods">
                            <small>Antimicrobianos e intervenciones. Se cruza por documento o historia clínica.</small>
                        </div>
                    </div>

                    <div class="imp-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload mr-1"></i> Procesar archivos
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@stop

@section('css')
<style>
    /* ── Encabezado ─────────────────────────────────────────────── */
    .imp-head { margin-bottom: .25rem; }
    .imp-title {
        font-size: 1.6rem; font-weight: 700; color: #262b34; margin: 0;
    }
    .imp-sub {
        margin: .3rem 0 0; color: #6b7280; font-size: .9rem;
    }

    /* ── Bento grid ─────────────────────────────────────────────── */
    .imp-bento {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        align-items: start;
    }
    .imp-cell--full { grid-column: 1 / -1; }
    .imp-cell--half { grid-column: span 1; }

    @media (max-width: 767.98px) {
        .imp-bento { grid-template-columns: 1fr; }
        .imp-cell--half { grid-column: 1 / -1; }
    }

    /* ── Tarjetas ───────────────────────────────────────────────── */
    .imp-card {
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid #e5e7f0;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: none;
        height: 100%;
    }
    .imp-card--epi  { border-top: 3px solid #2a377e; }
    .imp-card--proa { border-top: 3px solid #2e7d5b; }
    .imp-card--warn { border-top: 3px solid #c2872a; }

    .imp-card-head {
        display: flex; align-items: flex-start; gap: 12px;
        margin-bottom: 16px;
    }
    .imp-card-head h3 {
        font-size: 1rem; font-weight: 700; color: #262b34;
        margin: 0; letter-spacing: .01em;
    }
    .imp-card-sub {
        margin: .3rem 0 0; font-size: .85rem; color: #6b7280; line-height: 1.45;
    }
    .imp-ico {
        flex: 0 0 auto;
        width: 38px; height: 38px; border-radius: 11px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #eef1fa; color: #2a377e; font-size: 1rem;
    }
    .imp-ico--warn { background: #fdf4e3; color: #b3791f; }

    /* ── Estadísticas ───────────────────────────────────────────── */
    .imp-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .imp-stat {
        background: #f7f8fb;
        border: 1px solid #edeff6;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .imp-stat.is-warn { background: #fdf6e9; border-color: #f2e3c4; }
    .imp-stat-num {
        display: block; font-size: 1.5rem; font-weight: 700;
        color: #262b34; line-height: 1.1;
    }
    .imp-stat.is-warn .imp-stat-num { color: #b3791f; }
    .imp-stat-lbl {
        display: block; margin-top: 2px;
        font-size: .74rem; text-transform: uppercase; letter-spacing: .05em;
        color: #8b93a5; font-weight: 600;
    }

    /* ── Lista de advertencias ──────────────────────────────────── */
    .imp-avisos {
        max-height: 340px; overflow-y: auto;
        border: 1px solid #efe4cd; border-radius: 12px;
        background: #fffdf8;
    }
    .imp-aviso {
        padding: 9px 14px;
        font-size: .83rem; line-height: 1.45; color: #5c534a;
        border-bottom: 1px solid #f4ecdd;
    }
    .imp-aviso:last-child { border-bottom: 0; }
    .imp-avisos::-webkit-scrollbar { width: 10px; }
    .imp-avisos::-webkit-scrollbar-thumb {
        background: #e0cfa8; border-radius: 8px;
    }

    /* ── Formulario ─────────────────────────────────────────────── */
    .imp-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }
    @media (max-width: 767.98px) {
        .imp-fields { grid-template-columns: 1fr; }
        .imp-stats  { grid-template-columns: 1fr; }
    }
    .imp-field label {
        display: block; font-size: .8rem; font-weight: 700;
        color: #454b56; margin-bottom: 6px; text-transform: uppercase;
        letter-spacing: .04em;
    }
    .imp-field small {
        display: block; margin-top: 6px; color: #8b93a5; font-size: .78rem;
    }
    .imp-req, .imp-opt {
        display: inline-block; margin-left: 6px; padding: 1px 7px;
        border-radius: 999px; font-size: .65rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
    }
    .imp-req { background: #eef1fa; color: #2a377e; }
    .imp-opt { background: #f2f3f6; color: #8b93a5; }

    .imp-actions {
        margin-top: 20px; padding-top: 18px;
        border-top: 1px solid #eef0f5;
        display: flex; justify-content: flex-end;
    }

    /* ── Flashes ────────────────────────────────────────────────── */
    .imp-flash {
        display: flex; align-items: flex-start; gap: 10px;
        border-radius: 14px; padding: 14px 18px;
        font-size: .9rem; border: 1px solid transparent;
    }
    .imp-flash i { font-size: 1.05rem; margin-top: 1px; }
    .imp-flash--ok  { background: #edf7f1; border-color: #cfe8db; color: #1f6146; }
    .imp-flash--err { background: #fdeeee; border-color: #f3cdcd; color: #96322f; }
</style>
@stop
