@extends('adminlte::page')

@section('title', 'Crear Frecuencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus"></i> Crear Frecuencia</h1>
        <a href="{{ route('frecuencia.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-chart-bar"></i> Información de la Frecuencia
                    </h3>
                </div>
                <form action="{{ route('frecuencia.store') }}" method="POST" id="create-form">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="required">
                                <i class="fas fa-file-alt"></i> Descripción
                            </label>
                            <textarea 
                                name="descripcion" 
                                id="descripcion" 
                                class="form-control @error('descripcion') is-invalid @enderror" 
                                rows="3" 
                                maxlength="150" 
                                placeholder="Ingrese la descripción de la frecuencia (ej: Diaria, Semanal, Cada 8 horas)..."
                                required>{{ old('descripcion') }}</textarea>
                            <small class="form-text text-muted">
                                <span id="char-count">0</span>/150 caracteres
                            </small>
                            @error('descripcion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> La descripción debe ser única en el sistema y no puede exceder los 150 caracteres. 
                            Ejemplos: "Diaria", "Semanal", "Mensual", "Cada 8 horas", "Según necesidad".
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('frecuencia.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Guardar Frecuencia
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
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        #char-count {
            font-weight: bold;
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
            
            function updateCharCount() {
                const currentLength = descripcionInput.val().length;
                charCount.text(currentLength);
                
                if (currentLength > 150) {
                    charCount.css('color', 'red');
                } else if (currentLength > 120) {
                    charCount.css('color', 'orange');
                } else {
                    charCount.css('color', 'green');
                }
            }
            
            descripcionInput.on('input', updateCharCount);
            updateCharCount(); // Inicializar contador
            
            // Validación del formulario
            $('#create-form').on('submit', function(e) {
                e.preventDefault();
                
                const descripcion = descripcionInput.val().trim();
                
                // Validaciones frontend
                if (!descripcion) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción es obligatoria.'
                    });
                    descripcionInput.focus();
                    return;
                }
                
                if (descripcion.length > 150) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción no puede exceder los 150 caracteres.'
                    });
                    descripcionInput.focus();
                    return;
                }
                
                // Deshabilitar botón de envío
                const submitBtn = $('#submit-btn');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                
                // Enviar formulario
                this.submit();
            });
            
            // Mostrar mensajes de error
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
        });
    </script>
@stop
