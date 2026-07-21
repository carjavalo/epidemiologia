@extends('adminlte::page')

@section('title', 'Nueva Especialidad Tratante')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-md"></i> Nueva Especialidad Tratante</h1>
        <a href="{{ route('esp-tratante.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Crear Nueva Especialidad Tratante
                    </h3>
                </div>
                <form action="{{ route('esp-tratante.store') }}" method="POST" id="esp-tratante-form">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="form-label required">
                                <i class="fas fa-stethoscope"></i> Descripción de la Especialidad Tratante
                            </label>
                            <input type="text" 
                                   class="form-control @error('descripcion') is-invalid @enderror" 
                                   id="descripcion" 
                                   name="descripcion" 
                                   value="{{ old('descripcion') }}" 
                                   placeholder="Ingrese la descripción de la especialidad tratante"
                                   maxlength="150"
                                   required>
                            <div class="form-text">
                                <small class="text-muted">
                                    <span id="char-count">0</span>/150 caracteres
                                </small>
                            </div>
                            @error('descripcion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> La descripción debe ser única y no puede exceder los 150 caracteres.
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('esp-tratante.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Guardar Especialidad Tratante
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
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .alert-info {
            border-left: 4px solid #17a2b8;
        }
        #char-count {
            font-weight: bold;
        }
        .char-warning {
            color: #ffc107 !important;
        }
        .char-danger {
            color: #dc3545 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Contador de caracteres
            const descripcionInput = $('#descripcion');
            const charCount = $('#char-count');
            const maxLength = 150;

            function updateCharCount() {
                const currentLength = descripcionInput.val().length;
                charCount.text(currentLength);
                
                // Cambiar color según la proximidad al límite
                charCount.removeClass('char-warning char-danger');
                if (currentLength > maxLength * 0.8) {
                    charCount.addClass('char-warning');
                }
                if (currentLength > maxLength * 0.95) {
                    charCount.addClass('char-danger');
                }
            }

            // Actualizar contador en tiempo real
            descripcionInput.on('input', updateCharCount);
            
            // Inicializar contador
            updateCharCount();

            // Validación del formulario
            $('#esp-tratante-form').on('submit', function(e) {
                const descripcion = descripcionInput.val().trim();
                
                if (descripcion === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción de la especialidad tratante es obligatoria.'
                    });
                    descripcionInput.focus();
                    return false;
                }

                if (descripcion.length > maxLength) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: `La descripción no puede exceder los ${maxLength} caracteres.`
                    });
                    descripcionInput.focus();
                    return false;
                }

                // Deshabilitar botón de envío para evitar doble envío
                $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            });

            // Mostrar mensajes de error si existen
            @if($errors->any())
                let errorMessages = '';
                @foreach($errors->all() as $error)
                    errorMessages += '{{ $error }}\n';
                @endforeach
                
                Swal.fire({
                    icon: 'error',
                    title: 'Errores de Validación',
                    text: errorMessages
                });
            @endif

            // Mostrar mensaje de error de sesión
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}'
                });
            @endif

            // Auto-focus en el campo de descripción
            descripcionInput.focus();
        });
    </script>
@stop
