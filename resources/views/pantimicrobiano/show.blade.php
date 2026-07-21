@extends('adminlte::page')

@section('title', 'Ver Perfil Antimicrobiano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-shield-virus"></i> Perfil Antimicrobiano #{{ $pantimicrobiano->id }}</h1>
        <div class="btn-group">
            <a href="{{ route('pantimicrobiano.edit', $pantimicrobiano) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('pantimicrobiano.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Detalles del Perfil Antimicrobiano
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-hashtag"></i> ID</label>
                                <p class="form-control-static">{{ $pantimicrobiano->id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-edit"></i> Descripción</label>
                                <p class="form-control-static">{{ $pantimicrobiano->descripcion }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-plus"></i> Fecha de Creación</label>
                                <p class="form-control-static">{{ $pantimicrobiano->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-edit"></i> Última Actualización</label>
                                <p class="form-control-static">{{ $pantimicrobiano->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pantimicrobiano.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('pantimicrobiano.edit', $pantimicrobiano) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-delete" 
                                    data-url="{{ route('pantimicrobiano.destroy', $pantimicrobiano) }}"
                                    data-descripcion="{{ $pantimicrobiano->descripcion }}">
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
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .form-control-static {
            padding: 0.375rem 0;
            margin-bottom: 0;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: transparent;
            border: none;
        }
        .btn-group .btn {
            margin-left: 0.25rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Función para eliminar registro
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const descripcion = $(this).data('descripcion');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el perfil antimicrobiano "${descripcion}"?`,
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
                                    Swal.fire('Eliminado', response.message, 'success').then(() => {
                                        window.location.href = '{{ route("pantimicrobiano.index") }}';
                                    });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Error al eliminar el registro', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
