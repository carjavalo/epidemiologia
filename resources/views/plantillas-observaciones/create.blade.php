@extends('adminlte::page')

@section('title', 'Nueva Plantilla de Observación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nueva Plantilla de Observación</h1>
        <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Crear Nueva Plantilla de Observación</h3>
        </div>
        <form action="{{ route('plantillas-observaciones.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion" class="required">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="6"
                                      placeholder="Ingrese la descripción de la plantilla de observación"
                                      maxlength="65535"
                                      required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 65535 caracteres.</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Plantilla
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .required::after {
            content: " *";
            color: red;
        }
        .form-group label {
            font-weight: 600;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Contador de caracteres para descripción
            $('#descripcion').on('input', function() {
                const maxLength = 65535;
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;
                
                // Actualizar o crear contador
                let counter = $(this).siblings('.char-counter');
                if (counter.length === 0) {
                    counter = $('<small class="form-text text-muted char-counter"></small>');
                    $(this).after(counter);
                }
                
                counter.text(`${currentLength}/${maxLength} caracteres`);
                
                if (remaining < 100) {
                    counter.removeClass('text-muted').addClass('text-warning');
                }
                if (remaining < 50) {
                    counter.removeClass('text-warning').addClass('text-danger');
                }
                if (remaining >= 100) {
                    counter.removeClass('text-warning text-danger').addClass('text-muted');
                }
            });

            // Validación del formulario
            $('form').on('submit', function(e) {
                let isValid = true;

                // Validar descripción
                const descripcion = $('#descripcion').val().trim();
                if (descripcion === '') {
                    $('#descripcion').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#descripcion').removeClass('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error de Validación',
                        text: 'Por favor, complete todos los campos requeridos.',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        });
    </script>
@stop
