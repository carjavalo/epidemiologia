@extends('adminlte::page')

@section('title', 'Nueva equivalencia')

@section('content_header')
    <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-exchange-alt mr-2"></i>Nueva equivalencia</h1>
@stop

@section('content')
    @include('equivalencias.partials.avisos')

    <div class="card" style="max-width:680px;">
        <div class="card-body">
            <form method="POST" action="{{ route('equivalencias.store') }}">
                @csrf
                @include('equivalencias.partials.form', ['equivalencia' => null])

                <div class="d-flex justify-content-end mt-3" style="gap:10px;">
                    <a href="{{ route('equivalencias.index') }}" class="r-btn-tenue" style="color:#6b7280;">Cancelar</a>
                    <button type="submit" class="r-btn-guardar">
                        <i class="fas fa-save mr-1"></i> Guardar equivalencia
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
