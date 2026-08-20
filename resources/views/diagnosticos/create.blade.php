@extends('adminlte::page')

@section('title', 'Nuevo diagnóstico')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-notes-medical"></i> Nuevo diagnóstico</h1>
        <a href="{{ route('diagnosticos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Crear diagnóstico (CIE-10)</h3></div>
                <form action="{{ route('diagnosticos.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="codigo" class="required">Código CIE-10</label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                       id="codigo" name="codigo" value="{{ old('codigo') }}"
                                       placeholder="Ej: A09" maxlength="10" required>
                                @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8 form-group">
                                <label for="descripcion" class="required">Descripción</label>
                                <input type="text" class="form-control @error('descripcion') is-invalid @enderror"
                                       id="descripcion" name="descripcion" value="{{ old('descripcion') }}"
                                       placeholder="Descripción del diagnóstico" maxlength="500" required>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> El código CIE-10 debe ser único.
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('diagnosticos.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar diagnóstico</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
