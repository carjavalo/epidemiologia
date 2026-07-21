@extends('adminlte::page')

@section('title', 'Ver Procedimiento')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Procedimiento #{{ $procedimiento->id_procedimiento }}</h1>
        <div>
            <a href="{{ route('procedimientos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('procedimientos.edit', $procedimiento->id_procedimiento) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <button type="button" class="btn btn-primary" onclick="window.print();">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <!-- title row -->
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-hospital"></i> Hospital Universitario del Valle
                            <small class="float-right">Fecha: {{ $procedimiento->fecha_procedimiento->format('d/m/Y H:i') }}</small>
                        </h4>
                    </div>
                    <!-- /.col -->
                </div>
                
                <!-- info row -->
                <div class="row invoice-info mt-4">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>Programa de uso Optimizado de Antimicrobianos</strong><br>
                            <strong>PROA</strong><br>
                            Calle 5 # 36-08, Cali<br>
                            Valle del Cauca, Colombia<br>
                            Teléfono: (57) 2-620 6000<br>
                            Email: info@huv.gov.co
                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>Información del Procedimiento</strong><br>
                            <b>ID:</b> #{{ $procedimiento->id_procedimiento }}<br>
                            <b>Fecha:</b> {{ $procedimiento->fecha_procedimiento->format('d/m/Y H:i') }}<br>
                            <b>Procedimiento:</b> {{ $procedimiento->Nom_procedimiento }}<br>
                            <b>Total Registros:</b> {{ $procedimiento->detalles->count() }}
                        </address>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 invoice-col">
                        <div class="text-right mt-3">
                            <img src="{{ asset('images/logo_huv.png') }}" alt="Logo HUV" style="max-height: 100px;">
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                
                <!-- Table row -->
                <div class="row mt-4">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped notranslate">
                            <thead>
                                <tr class="notranslate">
                                    <th class="notranslate">Cod_Episodio</th>
                                    <th class="notranslate">Num_Ident</th>
                                    <th class="notranslate">F_Ingreso</th>
                                    <th class="notranslate">Servicio</th>
                                    <th class="notranslate">Diagnostico</th>
                                    <th class="notranslate">Antimicrobiano</th>
                                    <th class="notranslate">Via_Aplicacion</th>
                                    <th class="notranslate">Fec_Sumistro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($procedimiento->detalles as $detalle)
                                    <tr class="notranslate">
                                        <td>{{ $detalle->Cod_Episodio }}</td>
                                        <td>{{ $detalle->Num_Ident }}</td>
                                        <td>{{ $detalle->F_Ingreso ? $detalle->F_Ingreso->format('Y-m-d H:i:s') : '' }}</td>
                                        <td>{{ $detalle->Servicio }}</td>
                                        <td>{{ $detalle->Diagnostico }}</td>
                                        <td>{{ $detalle->Antimicrobiano }}</td>
                                        <td>{{ $detalle->Via_Aplicacion }}</td>
                                        <td>{{ $detalle->Fec_Sumistro ? $detalle->Fec_Sumistro->format('Y-m-d') : '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No hay detalles asociados a este procedimiento</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                
                <div class="row mt-4">
                    <!-- Stats -->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">Estadísticas del Procedimiento</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Registros</span>
                                                <span class="info-box-number">{{ $procedimiento->detalles->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-pills"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Antimicrobianos</span>
                                                <span class="info-box-number">{{ $procedimiento->detalles->unique('Antimicrobiano')->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Servicios -->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">Resumen por Servicio</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Servicio</th>
                                            <th>Cantidad</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $servicios = $procedimiento->detalles->groupBy('Servicio');
                                            $total = $procedimiento->detalles->count();
                                        @endphp
                                        
                                        @foreach($servicios as $servicio => $detalles)
                                            <tr>
                                                <td>{{ $servicio }}</td>
                                                <td>{{ $detalles->count() }}</td>
                                                <td>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-primary" style="width: {{ $total > 0 ? ($detalles->count() / $total * 100) : 0 }}%"></div>
                                                    </div>
                                                    <span class="badge bg-primary">{{ $total > 0 ? number_format($detalles->count() / $total * 100, 1) : 0 }}%</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between mt-4">
                            <p class="lead">Notas:</p>
                            <div>
                                <form action="{{ route('procedimientos.destroy', $procedimiento->id_procedimiento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este procedimiento? Esta acción también eliminará todos los detalles asociados.')">
                                        <i class="fas fa-trash"></i> Eliminar Procedimiento
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <p class="text-muted">
                                    Este procedimiento fue creado en el sistema PROA del Hospital Universitario del Valle.
                                    La información contenida es confidencial y solo debe ser utilizada por personal autorizado.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                
            </div>
            <!-- /.invoice -->
        </div>
    </div>
@stop

@section('css')
    <style>
        @media print {
            .no-print, .main-sidebar, .main-header, .content-header, .main-footer {
                display: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
                padding-top: 0 !important;
            }
            .invoice {
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Vista de procedimiento cargada!');
    </script>
@stop 