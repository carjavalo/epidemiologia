@extends('adminlte::page')
@section('title', 'Editar servicio')
@section('content_header')
    <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-hospital-alt mr-2"></i>Editar servicio</h1>
@stop
@section('content')
    @include('servicios.partials.avisos')
    <div class="card" style="max-width:620px;">
        <div class="card-body">
            <form method="POST" action="{{ route('servicios.update', $servicio) }}">
                @csrf @method('PUT')
                @include('servicios.partials.form')
                <p class="r-agrupar-pista mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Si cambias el nombre, las equivalencias que apuntaban al anterior se reapuntan solas.
                </p>
                <div class="d-flex justify-content-end mt-3" style="gap:10px;">
                    <a href="{{ route('servicios.index') }}" class="r-btn-tenue" style="color:#6b7280;">Cancelar</a>
                    <button type="submit" class="r-btn-guardar"><i class="fas fa-save mr-1"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
@stop
