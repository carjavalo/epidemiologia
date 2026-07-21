@extends('adminlte::page')

@section('title', 'Ver Perfil')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-cog"></i> Perfil #{{ isset($perfil) ? $perfil->id : 'N/A' }}</h1>
        <div class="btn-group">
            @if(isset($perfil) && $perfil->exists)
            <a href="{{ route('perfiles.edit', $perfil) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endif
            <a href="{{ route('perfiles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if(isset($perfil) && $perfil->exists)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Detalles del Perfil
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-hashtag"></i> ID:</label>
                                <p class="form-control-static">{{ $perfil->id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-edit"></i> Descripción:</label>
                                <p class="form-control-static">{{ $perfil->descripcion }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-plus"></i> Fecha de Creación:</label>
                                <p class="form-control-static">{{ $perfil->created_at ? $perfil->created_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-check"></i> Última Actualización:</label>
                                <p class="form-control-static">{{ $perfil->updated_at ? $perfil->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h4>Error</h4>
                        <p>No se pudo cargar el perfil solicitado. Por favor, regrese al listado e intente nuevamente.</p>
                        <a href="{{ route('perfiles.index') }}" class="btn btn-primary">Volver al Listado</a>
                    </div>
                </div>
            @endif
                @if(isset($perfil) && $perfil->exists)
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('perfiles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('perfiles.edit', $perfil) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Perfil
                            </a>
                            <button type="button"
                                    class="btn btn-danger btn-delete"
                                    data-url="{{ route('perfiles.destroy', $perfil) }}"
                                    data-id="{{ $perfil->id }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-title {
            font-weight: 600;
            color: #495057;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .form-control-static {
            padding: 8px 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 0;
            font-size: 14px;
            color: #495057;
        }
        
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .btn {
            font-weight: 500;
        }
        
        .btn-group .btn {
            margin-left: 5px;
        }
        
        .btn-group .btn:first-child {
            margin-left: 0;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Manejar eliminación con SweetAlert
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const id = $(this).data('id');
                
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
                            url: url,
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
                                        window.location.href = '{{ route('perfiles.index') }}';
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
                                    'Error al eliminar el perfil',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
            
            // Mostrar mensajes de éxito/error
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    showConfirmButton: true
                });
            @endif
        });
    </script>
@stop
