@extends('adminlte::page')

@section('title', 'Detalles de Plantilla de Observación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalles de Plantilla de Observación</h1>
        <div>
            <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            @if(isset($plantillaObservacion) && $plantillaObservacion->exists)
                <a href="{{ route('plantillas-observaciones.edit', $plantillaObservacion->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
        </div>
    </div>
@stop

@section('content')
    @if(isset($plantillaObservacion) && $plantillaObservacion->exists)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> 
                            Información de la Plantilla de Observación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>ID:</strong></label>
                                    <p class="form-control-static">{{ $plantillaObservacion->id }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Descripción:</strong></label>
                                    <div class="border rounded p-3 bg-light">
                                        <p class="mb-0">{{ $plantillaObservacion->descripcion }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> 
                            Información del Sistema
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><strong>Fecha de Creación:</strong></label>
                            <p class="form-control-static">
                                {{ $plantillaObservacion->created_at ? $plantillaObservacion->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Última Actualización:</strong></label>
                            <p class="form-control-static">
                                {{ $plantillaObservacion->updated_at ? $plantillaObservacion->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Longitud de Descripción:</strong></label>
                            <p class="form-control-static">
                                {{ strlen($plantillaObservacion->descripcion) }} caracteres
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs"></i> 
                            Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('plantillas-observaciones.edit', $plantillaObservacion->id) }}" 
                               class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Editar Plantilla
                            </a>
                            
                            <form action="{{ route('plantillas-observaciones.destroy', $plantillaObservacion->id) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-danger btn-block btn-delete"
                                        data-plantilla-name="ID: {{ $plantillaObservacion->id }}">
                                    <i class="fas fa-trash"></i> Eliminar Plantilla
                                </button>
                            </form>
                            
                            <a href="{{ route('plantillas-observaciones.index') }}" 
                               class="btn btn-secondary btn-block">
                                <i class="fas fa-list"></i> Ver Todas las Plantillas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4><i class="icon fas fa-ban"></i> Error!</h4>
            La plantilla de observación solicitada no existe o no se pudo cargar.
            <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    @endif
@stop

@section('css')
    <style>
        .form-control-static {
            padding: 7px 0;
            margin-bottom: 0;
            min-height: 34px;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .btn-block {
            width: 100%;
            margin-bottom: 10px;
        }
        .btn-block:last-child {
            margin-bottom: 0;
        }
        .border {
            border: 1px solid #dee2e6 !important;
        }
        .bg-light {
            background-color: #f8f9fa !important;
        }
        .d-grid {
            display: grid;
        }
        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Manejar eliminación con confirmación
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const plantillaName = $(this).data('plantilla-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la plantilla de observación "${plantillaName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
