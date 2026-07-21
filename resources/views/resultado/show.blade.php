@extends('adminlte::page')

@section('title', 'Ver Resultado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-clipboard-list"></i> Resultado #{{ $resultado->id }}</h1>
        <div class="btn-group">
            <a href="{{ route('resultado.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            <a href="{{ route('resultado.edit', $resultado) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
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
                        <i class="fas fa-info-circle"></i> Información del Resultado
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-hashtag"></i> ID:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge badge-primary">{{ $resultado->id }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-clipboard-list"></i> Descripción:</strong>
                        </div>
                        <div class="col-md-9">
                            <p class="text-muted mb-0">{{ $resultado->descripcion }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-plus"></i> Fecha de Creación:</strong>
                        </div>
                        <div class="col-md-9">
                            <p class="text-muted mb-0">
                                {{ $resultado->created_at->format('d/m/Y H:i:s') }}
                                <small class="text-muted">({{ $resultado->created_at->diffForHumans() }})</small>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-calendar-edit"></i> Última Actualización:</strong>
                        </div>
                        <div class="col-md-9">
                            <p class="text-muted mb-0">
                                {{ $resultado->updated_at->format('d/m/Y H:i:s') }}
                                <small class="text-muted">({{ $resultado->updated_at->diffForHumans() }})</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('resultado.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('resultado.edit', $resultado) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger" id="delete-btn" 
                                    data-url="{{ route('resultado.destroy', $resultado) }}"
                                    data-descripcion="{{ $resultado->descripcion }}">
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
                        <i class="fas fa-chart-bar"></i> Estadísticas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Días desde creación</span>
                            <span class="info-box-number">{{ $resultado->created_at->diffInDays(now()) }}</span>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-text-width"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Caracteres en descripción</span>
                            <span class="info-box-number">{{ strlen($resultado->descripcion) }}</span>
                        </div>
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
                        <a href="{{ route('resultado.edit', $resultado) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Editar Resultado
                        </a>
                        <a href="{{ route('resultado.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Crear Nuevo Resultado
                        </a>
                        <a href="{{ route('resultado.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-list"></i> Ver Todos los Resultados
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
        .info-box {
            margin-bottom: 15px;
        }
        .btn-block {
            margin-bottom: 10px;
        }
        hr {
            margin: 15px 0;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Manejar eliminación con confirmación
            $('#delete-btn').on('click', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var descripcion = $(this).data('descripcion');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el resultado "${descripcion}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear formulario para enviar DELETE
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': url
                        });
                        
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));
                        
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));
                        
                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // Mostrar mensajes de éxito/error desde el servidor
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
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@stop
