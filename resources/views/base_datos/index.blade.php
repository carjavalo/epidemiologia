@extends('admin.layouts.master')

@section('title', 'Base de datos')

@section('content_header')
    <div class="bd-head">
        <h1 class="bd-title"><i class="fas fa-database mr-2"></i>Base de datos</h1>
        <p class="bd-sub">Diccionario de las tablas más importantes: el nombre real de cada campo y qué significa.</p>
    </div>
@stop

@section('content')

    {{-- Índice de tablas (saltos rápidos) --}}
    <div class="bd-index">
        @foreach($tablas as $t)
            <a href="#tabla-{{ $t['tabla'] }}" class="bd-chip" style="--c: {{ $t['color'] }};">
                <i class="fas {{ $t['icono'] }} mr-1"></i>{{ $t['titulo'] }}
                <span class="bd-chip-count">{{ $t['total_campos'] }}</span>
            </a>
        @endforeach
    </div>

    @foreach($tablas as $t)
        <div class="bd-card" id="tabla-{{ $t['tabla'] }}">
            <div class="bd-card-head" style="--c: {{ $t['color'] }};">
                <span class="bd-ico"><i class="fas {{ $t['icono'] }}"></i></span>
                <div class="bd-card-info">
                    <h3>{{ $t['titulo'] }}</h3>
                    <div class="bd-card-meta">
                        <code class="bd-tabla-nombre">{{ $t['tabla'] }}</code>
                        <span class="bd-card-count">{{ $t['total_campos'] }} campos</span>
                    </div>
                    <p class="bd-card-desc">{{ $t['descripcion'] }}</p>
                </div>
            </div>

            <div class="bd-tabla-wrap">
                <table class="bd-tabla">
                    <thead>
                        <tr>
                            <th style="width: 26%;">Campo</th>
                            <th style="width: 14%;">Tipo</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($t['columnas'] as $col)
                            <tr>
                                <td><code class="bd-campo">{{ $col['campo'] }}</code></td>
                                <td><span class="bd-tipo">{{ $col['tipo'] }}</span></td>
                                <td class="bd-desc {{ $col['descripcion'] === '—' ? 'bd-desc-vacia' : '' }}">{{ $col['descripcion'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@stop

@section('css')
<style>
    .bd-head { margin-bottom: .25rem; }
    .bd-title { font-size: 1.6rem; font-weight: 700; color: #262b34; margin: 0; }
    .bd-sub   { margin: .3rem 0 0; color: #6b7280; font-size: .9rem; }

    /* Índice de chips */
    .bd-index {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin: 0 0 18px;
    }
    .bd-chip {
        display: inline-flex; align-items: center;
        background: rgba(255,255,255,0.96);
        border: 1px solid #e5e7f0;
        border-left: 3px solid var(--c, #2a377e);
        border-radius: 10px;
        padding: 6px 12px;
        font-size: .84rem; font-weight: 600;
        color: var(--c, #2a377e);
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .bd-chip:hover { transform: translateY(-1px); background: #f3f5fc; color: var(--c, #2a377e); text-decoration: none; }
    .bd-chip-count {
        margin-left: 8px; background: #eef1fa; color: #2a377e;
        border-radius: 999px; padding: 1px 8px; font-size: .72rem; font-weight: 700;
    }

    /* Tarjeta de tabla */
    .bd-card {
        background: rgba(255,255,255,0.97);
        border: 1px solid #e5e7f0;
        border-radius: 16px;
        box-shadow: none;
        margin-bottom: 20px;
        overflow: hidden;
        scroll-margin-top: 80px;
    }
    .bd-card-head {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 18px 22px;
        border-top: 3px solid var(--c, #2a377e);
        background: #fbfcfe;
        border-bottom: 1px solid #eef0f5;
    }
    .bd-ico {
        flex: 0 0 auto;
        width: 42px; height: 42px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #eef1fa; /* respaldo si no hay color-mix */
        background: color-mix(in srgb, var(--c, #2a377e) 12%, #fff);
        color: var(--c, #2a377e); font-size: 1.15rem;
    }
    .bd-card-info h3 { font-size: 1.05rem; font-weight: 700; color: #262b34; margin: 0; }
    .bd-card-meta { display: flex; align-items: center; gap: 10px; margin: 4px 0; flex-wrap: wrap; }
    .bd-tabla-nombre { font-size: .8rem; background: #eef1fa; color: #2a377e; padding: 2px 8px; border-radius: 6px; }
    .bd-card-count { font-size: .78rem; color: #8b93a5; }
    .bd-card-desc { margin: 2px 0 0; font-size: .86rem; color: #5a6172; line-height: 1.5; }

    /* Tabla de campos */
    .bd-tabla-wrap { overflow-x: auto; }
    .bd-tabla { width: 100%; border-collapse: separate; border-spacing: 0; }
    .bd-tabla thead th {
        background: #f5f6fa; color: #5a6172;
        font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
        text-align: left; padding: 10px 16px; border-bottom: 1px solid #e5e7f0; white-space: nowrap;
    }
    .bd-tabla tbody td { padding: 9px 16px; border-bottom: 1px solid #f2f3f8; vertical-align: top; font-size: .86rem; }
    .bd-tabla tbody tr:last-child td { border-bottom: 0; }
    .bd-tabla tbody tr:hover { background: #f9fafd; }
    .bd-campo { font-size: .82rem; background: #f1f3f9; color: #2a377e; padding: 2px 7px; border-radius: 6px; font-weight: 600; }
    .bd-tipo {
        font-size: .74rem; color: #6b7280; background: #f4f5f8;
        border: 1px solid #e9ebf1; border-radius: 6px; padding: 1px 8px; white-space: nowrap;
    }
    .bd-desc { color: #454b56; line-height: 1.45; }
    .bd-desc-vacia { color: #b3b9c5; }
</style>
@stop
