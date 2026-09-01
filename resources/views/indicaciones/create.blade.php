@extends('adminlte::page')

@section('title', 'Nueva Indicación Terapéutica - Epidemiología Hospitalaria')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus-circle text-success"></i> Nueva Indicación Terapéutica</h1>
        <a href="{{ route('indicaciones.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-edit"></i> Formulario de Nueva Indicación
                    </h3>
                </div>
                <form action="{{ route('indicaciones.store') }}" method="POST" id="form-indicacion">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-pills text-primary"></i> Descripción de la Indicación Terapéutica
                                <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control @error('descripcion') is-invalid @enderror" 
                                id="descripcion" 
                                name="descripcion" 
                                rows="3" 
                                maxlength="150"
                                placeholder="Ingrese la descripción de la indicación terapéutica..."
                                required>{{ old('descripcion') }}</textarea>
                            <small class="form-text text-muted">
                                <span id="contador-caracteres">0</span>/150 caracteres
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
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('indicaciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" id="btn-guardar">
                                <i class="fas fa-save"></i> Guardar Indicación
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
        .card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        .alert-info {
            border-left: 4px solid #17a2b8;
            background-color: rgba(23, 162, 184, 0.1);
        }
        #contador-caracteres {
            font-weight: bold;
            color: #28a745;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Contador de caracteres
            $('#descripcion').on('input', function() {
                const length = $(this).val().length;
                $('#contador-caracteres').text(length);
                
                if (length > 140) {
                    $('#contador-caracteres').css('color', '#dc3545');
                } else if (length > 120) {
                    $('#contador-caracteres').css('color', '#ffc107');
                } else {
                    $('#contador-caracteres').css('color', '#28a745');
                }
            });

            // Validación del formulario
            $('#form-indicacion').on('submit', function(e) {
                const descripcion = $('#descripcion').val().trim();
                
                if (descripcion.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción es obligatoria.',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }
                
                if (descripcion.length > 150) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'La descripción no puede exceder los 150 caracteres.',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }

                // Mostrar loading
                $('#btn-guardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            });

            // Inicializar contador
            $('#descripcion').trigger('input');
        });

        // Mostrar mensajes de error del servidor
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        @endif
    </script>
@stop
