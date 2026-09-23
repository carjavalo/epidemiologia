@extends('adminlte::page')
@section('title', 'Nuevo servicio')
@section('content_header')
    <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-hospital-alt mr-2"></i>Nuevo servicio</h1>
@stop
@section('content')
    @include('servicios.partials.avisos')
    <div class="card" style="max-width:620px;">
        <div class="card-body">
            <form method="POST" action="{{ route('servicios.store') }}">
                @csrf
                @include('servicios.partials.form', ['servicio' => null])
                <div class="d-flex justify-content-end mt-3" style="gap:10px;">
                    <a href="{{ route('servicios.index') }}" class="r-btn-tenue" style="color:#6b7280;">Cancelar</a>
                    <button type="submit" class="r-btn-guardar"><i class="fas fa-save mr-1"></i> Guardar servicio</button>
                </div>
            </form>
        </div>
    </div>
@stop
