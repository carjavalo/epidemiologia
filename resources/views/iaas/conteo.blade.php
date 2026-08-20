@extends('adminlte::page')

@section('title', 'Conteo de IAAS')

@section('content_header')
    <h1><i class="fas fa-virus-slash"></i> Conteo de IAAS por fecha</h1>
@stop

@section('content')
    {{-- Filtro de rango personalizable --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Rango de fechas</h3></div>
        <div class="card-body">
            <form method="GET" action="{{ route('iaas.conteo') }}" class="form-row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Fecha inicial</label>
                    <input type="date" name="fecha_inicial" class="form-control" value="{{ $inicio->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="mb-1">Fecha final</label>
                    <input type="date" name="fecha_final" class="form-control" value="{{ $fin->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Consultar</button>
                    <a href="{{ route('iaas.conteo') }}" class="btn btn-secondary">Mes actual</a>
                </div>
            </form>
            <small class="text-muted d-block mt-1">
                Se filtra por <strong>fecha de toma de muestra</strong>, entre
                {{ $inicio->format('d/m/Y') }} y {{ $fin->format('d/m/Y') }}.
            </small>
        </div>
    </div>

    {{-- Métricas --}}
    <div class="row">
        <div class="col-md-4">
            <div class="iaas-stat iaas-stat--main">
                <span class="iaas-num">{{ $totalIaas }}</span>
                <span class="iaas-lbl">IAAS en el rango</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="iaas-stat">
                <span class="iaas-num">{{ $pacientesIaas }}</span>
                <span class="iaas-lbl">Pacientes con IAAS</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="iaas-stat">
                <span class="iaas-num">{{ $totalRegistros }}</span>
                <span class="iaas-lbl">Registros totales del rango</span>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-1"></i>
        <strong>IAAS</strong> = infección intrahospitalaria (tipo 1 y clasificación 3 «Infección», 5 «Infección sin Mios» o 6 «Infección Polimicrobiano»).
        Abajo el desglose completo por clasificación para verificar el criterio.
    </div>

    <div class="row">
        {{-- Desglose por servicio --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">IAAS por servicio</h3></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Servicio</th><th class="text-center">IAAS</th><th class="text-center">Pacientes</th></tr></thead>
                        <tbody>
                            @forelse($porServicio as $s)
                                <tr>
                                    <td>{{ $s->ubicacion ?: '(sin servicio)' }}</td>
                                    <td class="text-center">{{ $s->total }}</td>
                                    <td class="text-center">{{ $s->pacientes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">Sin IAAS en el rango.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Desglose por clasificación (transparencia) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Todos los registros por clasificación</h3></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Clasificación</th><th class="text-center">Registros</th></tr></thead>
                        <tbody>
                            @forelse($porClasificacion as $c)
                                <tr>
                                    <td>{{ $c->clasificacion }}</td>
                                    <td class="text-center">{{ $c->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">Sin registros en el rango.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .iaas-stat {
            background: #fff; border: 1px solid #e5e7f0; border-radius: 16px;
            padding: 18px 20px; margin-bottom: 16px; border-top: 3px solid #2a377e;
        }
        .iaas-stat--main { border-top-color: #b23b46; }
        .iaas-num { display: block; font-size: 2.1rem; font-weight: 700; color: #262b34; line-height: 1; }
        .iaas-stat--main .iaas-num { color: #b23b46; }
        .iaas-lbl { display: block; margin-top: 6px; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #8b93a5; font-weight: 600; }
    </style>
@stop
