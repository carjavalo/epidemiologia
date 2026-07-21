@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content_header')
    <div class="pa-dashboard">
        <h1 class="pa-dash-title">Datos estadísticos de los procedimientos</h1>
        <p class="pa-dash-sub">Resumen de actividad e importaciones del sistema.</p>
    </div>
@stop

@section('content')
    <div class="pa-dashboard">

    {{-- Tarjeta de bienvenida --}}
    <div class="row">
        <div class="col-12">
            <div class="pa-card pa-welcome mb-3">
                <div class="pa-ico"><i class="fas fa-hand-sparkles"></i></div>
                <div>
                    <h5>Bienvenido al sistema, {{ optional(auth()->user())->usuario ?? 'usuario' }}</h5>
                    <p>Has iniciado sesión correctamente. Todo está en orden.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- HISTORIAL DE ACTIVIDADES (Trazabilidad por usuario) — SOLO ADMIN --}}
    {{-- ============================================================= --}}
    @can('admin')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>Historial de actividades
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="form-row align-items-end mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold mb-1">Usuario</label>
                            <select name="user_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Todos los usuarios</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" {{ (string) $filtroUser === (string) $u->id ? 'selected' : '' }}>
                                        {{ trim($u->name . ' ' . ($u->apellido1 ?? '') . ' ' . ($u->apellido2 ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold mb-1">Tipo</label>
                            <select name="tipo" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Todos los tipos</option>
                                <option value="epidemiologia" {{ $filtroTipo === 'epidemiologia' ? 'selected' : '' }}>Epidemiología</option>
                                <option value="proa" {{ $filtroTipo === 'proa' ? 'selected' : '' }}>PROA</option>
                                <option value="importacion" {{ $filtroTipo === 'importacion' ? 'selected' : '' }}>Importación</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter mr-1"></i>Filtrar
                            </button>
                            @if($filtroUser || $filtroTipo)
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times mr-1"></i>Limpiar
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- Línea de tiempo --}}
                    @php
                        $iconMap = [
                            'epidemiologia' => ['fa-vial', 'bg-info'],
                            'proa'          => ['fa-capsules', 'bg-success'],
                            'importacion'   => ['fa-file-import', 'bg-warning'],
                            'general'       => ['fa-info', 'bg-secondary'],
                        ];
                    @endphp

                    @if($actividades->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle mr-2"></i>No hay actividades registradas
                            {{ ($filtroUser || $filtroTipo) ? 'con los filtros seleccionados.' : 'todavía. Cuando un usuario registre datos de epidemiología o PROA, aparecerán aquí.' }}
                        </div>
                    @else
                        <div class="timeline" style="max-height: 600px; overflow-y: auto;">
                            @php $diaActual = null; @endphp
                            @foreach($actividades as $act)
                                @php
                                    [$icon, $bg] = $iconMap[$act->tipo] ?? $iconMap['general'];
                                    $dia = $act->created_at?->format('d/m/Y');
                                @endphp

                                @if($dia !== $diaActual)
                                    @php $diaActual = $dia; @endphp
                                    <div class="time-label">
                                        <span class="bg-primary">{{ $dia }}</span>
                                    </div>
                                @endif

                                <div>
                                    <i class="fas {{ $icon }} {{ $bg }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="far fa-clock"></i> {{ $act->created_at?->format('H:i') }}
                                            <small class="text-muted">({{ $act->created_at?->diffForHumans() }})</small>
                                        </span>
                                        <h3 class="timeline-header">
                                            <i class="fas fa-user mr-1 text-muted"></i>{{ $act->user_nombre ?: 'Sistema' }}
                                        </h3>
                                        <div class="timeline-body">
                                            {{ $act->descripcion }}
                                            @if($act->paciente)
                                                <div class="mt-1"><small class="text-muted"><i class="fas fa-user-injured mr-1"></i>{{ $act->paciente }}</small></div>
                                            @endif
                                        </div>
                                        <div class="timeline-footer">
                                            <span class="badge {{ $bg }}">{{ ucfirst($act->tipo) }}</span>
                                            <span class="badge badge-light border">{{ ucfirst($act->accion) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div>
                                <i class="far fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endcan

    </div>{{-- /.pa-dashboard --}}
@stop

@section('extra_css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('¡Hola!'); </script>
@stop
