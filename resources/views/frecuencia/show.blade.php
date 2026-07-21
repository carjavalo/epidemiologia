@extends('adminlte::page')

@section('title', 'Detalles de la Frecuencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-eye"></i> Detalles de la Frecuencia</h1>
        <div>
            <a href="{{ route('frecuencia.edit', $frecuencia->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('frecuencia.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Información de la Frecuencia
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">ID: {{ $frecuencia->id }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-file-alt text-primary"></i> Descripción:
                                </label>
                                <div class="border rounded p-3 bg-light">
                                    {{ $frecuencia->descripcion }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-calendar-plus text-success"></i> Fecha de Creación:
                                </label>
                                <div class="border rounded p-2 bg-light">
                                    {{ $frecuencia->created_at_formatted }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-calendar-check text-info"></i> Última Actualización:
                                </label>
                                <div class="border rounded p-2 bg-light">
                                    {{ $frecuencia->updated_at_formatted }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-hashtag text-secondary"></i> ID de la Frecuencia:
                                </label>
                                <div class="border rounded p-2 bg-light">
                                    {{ $frecuencia->id }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-ruler text-warning"></i> Longitud de Descripción:
                                </label>
                                <div class="border rounded p-2 bg-light">
                                    {{ strlen($frecuencia->descripcion) }} / 150 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('frecuencia.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        <div>
                            <a href="{{ route('frecuencia.edit', $frecuencia->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Frecuencia
                            </a>
                            <button type="button" class="btn btn-danger" id="delete-btn" 
                                    data-url="{{ route('frecuencia.destroy', $frecuencia->id) }}"
                                    data-descripcion="{{ $frecuencia->descripcion }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información Adicional
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tiempo Transcurrido</span>
                            <span class="info-box-number">
                                {{ $frecuencia->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-edit"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Última Modificación</span>
                            <span class="info-box-number">
                                {{ $frecuencia->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Puedes editar esta frecuencia haciendo clic en el botón "Editar Frecuencia".
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('frecuencia.edit', $frecuencia->id) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Editar Frecuencia
                        </a>
                        <a href="{{ route('frecuencia.create') }}" 
                           class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Crear Nueva Frecuencia
                        </a>
                        <a href="{{ route('frecuencia.index') }}" 
                           class="btn btn-primary btn-block">
                            <i class="fas fa-list"></i> Ver Todas las Frecuencias
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }
        .badge {
            font-size: 0.9em;
        }
        .bg-light {
            background-color: #f8f9fa !important;
        }
        .info-box {
            margin-bottom: 1rem;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .btn-block {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Manejar eliminación
            $('#delete-btn').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const descripcion = $(this).data('descripcion');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la frecuencia "${descripcion}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Eliminado',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = '{{ route("frecuencia.index") }}';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al eliminar la frecuencia.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
