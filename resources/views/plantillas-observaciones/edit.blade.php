@extends('adminlte::page')

@section('title', 'Editar Plantilla de Observación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Editar Plantilla de Observación</h1>
        <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    @if(isset($plantillaObservacion) && $plantillaObservacion->exists)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar Plantilla de Observación #{{ $plantillaObservacion->id }}</h3>
            </div>
            <form action="{{ route('plantillas-observaciones.update', $plantillaObservacion->id) }}" method="POST">
                @csrf
                @method('PUT')
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
                                          required>{{ old('descripcion', $plantillaObservacion->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Máximo 65535 caracteres.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha de Creación</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $plantillaObservacion->created_at ? $plantillaObservacion->created_at->format('d/m/Y H:i:s') : 'No disponible' }}" 
                                       readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Última Actualización</label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $plantillaObservacion->updated_at ? $plantillaObservacion->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <a href="{{ route('plantillas-observaciones.show', $plantillaObservacion->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Plantilla
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="alert alert-danger">
            <h4><i class="icon fas fa-ban"></i> Error!</h4>
            La plantilla de observación solicitada no existe o no se pudo cargar.
            <a href="{{ route('plantillas-observaciones.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    @endif
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

            // Inicializar contadores al cargar la página
            $('#descripcion').trigger('input');
        });
    </script>
@stop
