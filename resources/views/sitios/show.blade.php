@extends('adminlte::page')
@section('title', $sitio->nombre)
@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-crosshairs mr-2"></i>{{ $sitio->nombre }}</h1>
        <a href="{{ route('sitios.edit', $sitio) }}" class="r-btn"><i class="fas fa-edit mr-1"></i> Editar</a>
    </div>
@stop
@section('content')
    <div class="r-ctx" style="position:static;">
        <a href="{{ route('sitios.index') }}" class="r-ctx-volver" title="Volver a sitios"><i class="fas fa-arrow-left"></i></a>
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta"><a href="{{ route('sitios.index') }}">Sitios</a></span>
            <h3>{{ $sitio->nombre }}</h3>
        </div>
        <div class="r-ctx-nums">
            <span class="r-chip r-chip--neut"><b>{{ $sitio->equivalencias->count() }}</b>&nbsp;equivalencias</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="r-sec-head r-sec-head--suelta">
                <h4>Textos de la fuente que llegan a este sitio</h4>
            </div>
            @forelse($sitio->equivalencias->sortBy('texto_crudo') as $eq)
                <span class="r-chip r-chip--blanco mr-1 mb-1" style="font-family:ui-monospace,Menlo,monospace;">{{ $eq->texto_crudo }}</span>
            @empty
                <p class="text-muted mb-0">
                    Ningún texto crudo apunta todavía a este sitio.
                    <a href="{{ route('equivalencias.create') }}">Crear una equivalencia</a>.
                </p>
            @endforelse
        </div>
    </div>
@stop
