@extends('adminlte::page')

@section('title', 'Detalle Indicación Terapéutica - ProAHUV')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-eye text-info"></i> Detalle de Indicación Terapéutica</h1>
        <div class="btn-group">
            <a href="{{ route('indicaciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            <a href="{{ route('indicaciones.edit', $indicacion->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información de la Indicación
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-pills"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">ID de la Indicación</span>
                                    <span class="info-box-number">#{{ $indicacion->id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-pills text-primary"></i> Descripción
                        </label>
                        <div class="alert alert-light border">
                            {{ $indicacion->descripcion }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-plus text-success"></i> Fecha de Creación
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" 
                                           value="{{ $indicacion->created_at->format('d/m/Y H:i:s') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-edit text-warning"></i> Última Actualización
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" 
                                           value="{{ $indicacion->updated_at->format('d/m/Y H:i:s') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Información adicional:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Longitud de la descripción: <strong>{{ strlen($indicacion->descripcion) }}</strong> caracteres</li>
                                    <li>Estado: <span class="badge badge-success">Activa</span></li>
                                    <li>Tiempo transcurrido desde creación: <strong>{{ $indicacion->created_at->diffForHumans() }}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('indicaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('indicaciones.edit', $indicacion->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger" onclick="eliminarIndicacion({{ $indicacion->id }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }
        .info-box {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .alert-light {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #495057;
            font-size: 1.1em;
            padding: 15px;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .btn-group .btn {
            margin-left: 5px;
        }
        .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
        }
        .alert-info {
            border-left: 4px solid #17a2b8;
            background-color: rgba(23, 162, 184, 0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function eliminarIndicacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/indicaciones/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Eliminado',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    window.location.href = '{{ route('indicaciones.index') }}';
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al eliminar la indicación',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@stop
