@extends('adminlte::page')
@section('title', $servicio->nombre)
@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-hospital-alt mr-2"></i>{{ $servicio->nombre }}</h1>
        <a href="{{ route('servicios.edit', $servicio) }}" class="r-btn"><i class="fas fa-edit mr-1"></i> Editar</a>
    </div>
@stop
@section('content')
    <div class="r-ctx" style="position:static;">
        <a href="{{ route('servicios.index') }}" class="r-ctx-volver" title="Volver a servicios"><i class="fas fa-arrow-left"></i></a>
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta"><a href="{{ route('servicios.index') }}">Servicios</a></span>
            <h3>{{ $servicio->nombre }}</h3>
        </div>
        <div class="r-ctx-nums">
            <span class="r-chip r-chip--neut"><b>{{ $servicio->equivalencias->count() }}</b>&nbsp;equivalencias</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="r-sec-head r-sec-head--suelta">
                <h4>Textos de la fuente que llegan a este servicio</h4>
            </div>
            @forelse($servicio->equivalencias->sortBy('texto_crudo') as $eq)
                <span class="r-chip r-chip--blanco mr-1 mb-1" style="font-family:ui-monospace,Menlo,monospace;">{{ $eq->texto_crudo }}</span>
            @empty
                <p class="text-muted mb-0">
                    Ningún texto crudo apunta todavía a este servicio.
                    <a href="{{ route('equivalencias.create') }}">Crear una equivalencia</a>.
                </p>
            @endforelse
        </div>
    </div>
@stop
