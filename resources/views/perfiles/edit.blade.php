@extends('adminlte::page')

@section('title', 'Editar Perfil')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit"></i> Editar Perfil #{{ isset($perfil) ? $perfil->id : 'N/A' }}</h1>
        <a href="{{ route('perfiles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Perfil #{{ isset($perfil) ? $perfil->id : 'N/A' }}
                    </h3>
                </div>
                @if(isset($perfil) && $perfil->exists)
                <form action="{{ route('perfiles.update', $perfil) }}" method="POST">
                @else
                <div class="alert alert-danger">
                    <h4>Error</h4>
                    <p>No se pudo cargar el perfil para editar. Por favor, regrese al listado e intente nuevamente.</p>
                    <a href="{{ route('perfiles.index') }}" class="btn btn-primary">Volver al Listado</a>
                </div>
                <form style="display: none;">
                @endif
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="required">
                                <i class="fas fa-edit"></i> Descripción
                            </label>
                            <input type="text"
                                   class="form-control @error('descripcion') is-invalid @enderror"
                                   id="descripcion"
                                   name="descripcion"
                                   value="{{ old('descripcion', isset($perfil) ? $perfil->descripcion : '') }}"
                                   placeholder="Ingrese la descripción del perfil"
                                   maxlength="150"
                                   required>
                            <small class="form-text text-muted">
                                Máximo 150 caracteres. <span id="char-count">0</span>/150
                            </small>
                            @error('descripcion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @if(isset($perfil) && $perfil->exists)
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Información del Registro</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Creado:</strong> {{ $perfil->created_at ? $perfil->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Actualizado:</strong> {{ $perfil->updated_at ? $perfil->updated_at->format('d/m/Y H:i:s') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('perfiles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Perfil
                            </button>
                        </div>
                    </div>
                </form>
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
        
        .required::after {
            content: " *";
            color: red;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .btn {
            font-weight: 500;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .invalid-feedback {
            font-weight: 500;
        }
        
        #char-count {
            font-weight: 600;
            color: #007bff;
        }
        
        .text-muted {
            font-size: 0.875rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Contador de caracteres
            $('#descripcion').on('input', function() {
                const currentLength = $(this).val().length;
                const maxLength = 150;
                $('#char-count').text(currentLength);
                
                if (currentLength > maxLength * 0.8) {
                    $('#char-count').css('color', '#dc3545');
                } else if (currentLength > maxLength * 0.6) {
                    $('#char-count').css('color', '#ffc107');
                } else {
                    $('#char-count').css('color', '#007bff');
                }
            });
            
            // Inicializar contador
            $('#descripcion').trigger('input');
            
            // Validación del formulario
            $('form').on('submit', function(e) {
                const descripcion = $('#descripcion').val().trim();
                
                if (descripcion === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: 'La descripción es obligatoria.',
                        confirmButtonText: 'Entendido'
                    });
                    $('#descripcion').focus();
                    return false;
                }
                
                if (descripcion.length > 150) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: 'La descripción no puede exceder los 150 caracteres.',
                        confirmButtonText: 'Entendido'
                    });
                    $('#descripcion').focus();
                    return false;
                }
            });
            
            // Mostrar mensajes de error si existen
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
