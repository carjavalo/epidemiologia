@extends('adminlte::page')

@section('title', 'Crear Perfil Antimicrobiano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-shield-virus"></i> Crear Perfil Antimicrobiano</h1>
        <a href="{{ route('pantimicrobiano.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-plus"></i> Nuevo Perfil Antimicrobiano
                    </h3>
                </div>
                <form action="{{ route('pantimicrobiano.store') }}" method="POST">
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
                                   placeholder="Ingrese la descripción del perfil antimicrobiano"
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
                            <a href="{{ route('pantimicrobiano.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Perfil Antimicrobiano
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
        .required::after {
            content: " *";
            color: red;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        #char-count {
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Contador de caracteres
            $('#descripcion').on('input', function() {
                const currentLength = $(this).val().length;
                $('#char-count').text(currentLength);
                
                if (currentLength > 140) {
                    $('#char-count').addClass('text-warning');
                } else {
                    $('#char-count').removeClass('text-warning');
                }
                
                if (currentLength >= 150) {
                    $('#char-count').addClass('text-danger').removeClass('text-warning');
                } else {
                    $('#char-count').removeClass('text-danger');
                }
            });

            // Validación del formulario
            $('form').on('submit', function(e) {
                const descripcion = $('#descripcion').val().trim();
                
                if (descripcion === '') {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'La descripción es obligatoria.',
                        icon: 'error'
                    });
                    return false;
                }
                
                if (descripcion.length > 150) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'La descripción no puede exceder los 150 caracteres.',
                        icon: 'error'
                    });
                    return false;
                }
            });

            // Inicializar contador
            $('#descripcion').trigger('input');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
