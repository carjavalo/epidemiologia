@extends('adminlte::page')

@section('title', 'Editar Procedimiento')

@section('content_header')
    <h1>Editar Procedimiento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('procedimientos.update', $procedimiento->id_procedimiento) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="id_procedimiento">ID del Procedimiento</label>
                    <input type="text" id="id_procedimiento" class="form-control" value="{{ $procedimiento->id_procedimiento }}" readonly disabled>
                    <small class="form-text text-muted">Este campo no se puede editar</small>
                </div>
                
                <div class="form-group">
                    <label for="fecha_procedimiento">Fecha del Procedimiento</label>
                    <input type="datetime-local" name="fecha_procedimiento" id="fecha_procedimiento" class="form-control @error('fecha_procedimiento') is-invalid @enderror" value="{{ old('fecha_procedimiento', $procedimiento->fecha_procedimiento->format('Y-m-d\TH:i')) }}" required>
                    @error('fecha_procedimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="Nom_procedimiento">Nombre del Procedimiento</label>
                    <input type="text" name="Nom_procedimiento" id="Nom_procedimiento" class="form-control @error('Nom_procedimiento') is-invalid @enderror" value="{{ old('Nom_procedimiento', $procedimiento->Nom_procedimiento) }}" required>
                    @error('Nom_procedimiento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('procedimientos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Formulario de edición cargado!');
    </script>
@stop 