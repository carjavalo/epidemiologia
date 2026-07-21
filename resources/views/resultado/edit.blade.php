@extends('adminlte::page')

@section('title', 'Editar Resultado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-clipboard-list"></i> Editar Resultado #{{ $resultado->id }}</h1>
        <a href="{{ route('resultado.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-edit"></i> Editar Resultado #{{ $resultado->id }}
                    </h3>
                </div>
                <form action="{{ route('resultado.update', $resultado) }}" method="POST" id="resultado-form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="form-label required">
                                <i class="fas fa-clipboard-list"></i> Descripción del Resultado
                            </label>
                            <input type="text" 
                                   class="form-control @error('descripcion') is-invalid @enderror" 
                                   id="descripcion" 
                                   name="descripcion" 
                                   value="{{ old('descripcion', $resultado->descripcion) }}" 
                                   placeholder="Ingrese la descripción del resultado"
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-calendar-plus"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fecha de Creación</span>
                                        <span class="info-box-number">{{ $resultado->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-calendar-edit"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Última Actualización</span>
                                        <span class="info-box-number">{{ $resultado->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('resultado.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> Actualizar Resultado
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
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .alert {
            border-left: 4px solid #17a2b8;
        }
        #char-count {
            font-weight: 600;
        }
        .text-warning {
            color: #ffc107 !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .info-box {
            margin-bottom: 15px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            const descripcionInput = $('#descripcion');
            const charCount = $('#char-count');
            const maxLength = 150;

            // Contador de caracteres en tiempo real
            descripcionInput.on('input', function() {
                const currentLength = $(this).val().length;
                charCount.text(currentLength);
                
                // Cambiar color según la longitud
                charCount.removeClass('text-warning text-danger');
                if (currentLength > 120) {
                    charCount.addClass('text-warning');
                }
                if (currentLength > 140) {
                    charCount.addClass('text-danger').removeClass('text-warning');
                }
            });

            // Inicializar contador con valor existente
            const initialLength = descripcionInput.val().length;
            charCount.text(initialLength);
            if (initialLength > 120) {
                charCount.addClass('text-warning');
            }
            if (initialLength > 140) {
                charCount.addClass('text-danger').removeClass('text-warning');
            }

            // Validación del formulario
            $('#resultado-form').on('submit', function(e) {
                const descripcion = descripcionInput.val().trim();
                
                if (descripcion === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción del resultado es obligatoria.'
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
                $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
            });

            // Mostrar mensajes de error del servidor
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}'
                });
            @endif

            // Enfocar el campo de descripción al cargar la página
            descripcionInput.focus();
        });
    </script>
@stop
