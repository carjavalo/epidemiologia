@extends('adminlte::page')

@section('title', 'Nuevo Perfil')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus"></i> Nuevo Perfil</h1>
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
                        <i class="fas fa-plus"></i> Nuevo Perfil
                    </h3>
                </div>
                <form action="{{ route('perfiles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="required">
                                <i class="fas fa-edit"></i> Descripción
                            </label>
                            <input type="text" 
                                   class="form-control @error('descripcion') is-invalid @enderror" 
                                   id="descripcion" 
                                   name="descripcion" 
                                   value="{{ old('descripcion') }}" 
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
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('perfiles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Perfil
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
