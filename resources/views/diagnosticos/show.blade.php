@extends('adminlte::page')

@section('title', 'Detalle del diagnóstico')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-notes-medical"></i> Detalle del diagnóstico</h1>
        <a href="{{ route('diagnosticos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ $diagnostico->codigo }}</h3></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><th style="width:180px">Código CIE-10</th><td>{{ $diagnostico->codigo }}</td></tr>
                        <tr><th>Descripción</th><td>{{ $diagnostico->descripcion }}</td></tr>
                        <tr><th>Creado</th><td>{{ $diagnostico->created_at?->format('d/m/Y H:i') }}</td></tr>
                        <tr><th>Última actualización</th><td>{{ $diagnostico->updated_at?->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('diagnosticos.edit', $diagnostico) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
                </div>
            </div>
        </div>
    </div>
@stop
