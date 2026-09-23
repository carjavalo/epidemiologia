@extends('adminlte::page')

@section('title', 'Textos pendientes de estandarizar')

@section('content_header')
    <h1 style="font-size:1.6rem;margin-bottom:0;">
        <i class="fas fa-triangle-exclamation mr-2"></i>Textos pendientes de estandarizar
    </h1>
@stop

@section('content')

    @include('equivalencias.partials.avisos')

    <div class="r-ctx" style="position:static;">
        <a href="{{ route('equivalencias.index') }}" class="r-ctx-volver" title="Volver a equivalencias">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta"><a href="{{ route('equivalencias.index') }}">Equivalencias</a></span>
            <h3>Pendientes</h3>
        </div>
        <div class="r-ctx-nums">
            @foreach($pendientes as $catalogo => $filas)
                <span class="r-chip {{ count($filas) ? 'r-chip--pend' : 'r-chip--ok' }}">
                    <b>{{ count($filas) }}</b>&nbsp;{{ $catalogos[$catalogo] }}
                </span>
            @endforeach
        </div>
    </div>

    <div class="r-aviso r-aviso--info">
        <i class="fas fa-info-circle"></i>
        <span>
            <b>Qué es esto</b>
            Textos que aparecen en los registros ya cargados y que el sistema no supo estandarizar,
            porque nadie le ha dicho todavía a qué corresponden. Se quedan tal cual hasta que los asignes.
            Elige el valor de la derecha y guarda; después ejecuta
            <code>php artisan estandarizar:registros --aplicar</code> para actualizar los registros existentes.
        </span>
    </div>

    <form method="POST" action="{{ route('equivalencias.mapear') }}">
        @csrf

        @php $indice = 0; @endphp

        @foreach($pendientes as $catalogo => $filas)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="r-sec-head r-sec-head--suelta">
                        <h4>{{ $catalogos[$catalogo] }}</h4>
                        <span class="r-sec-nota">
                            {{ count($filas) }} {{ count($filas) === 1 ? 'texto sin asignar' : 'textos sin asignar' }}
                        </span>
                    </div>

                    @if(count($filas) === 0)
                        <p class="text-muted mb-0">
                            <i class="fas fa-check-circle mr-1" style="color:var(--r-ok-fg)"></i>
                            Todo el catálogo está cubierto.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Texto que llega de la fuente</th>
                                        <th style="width:110px;" class="text-center">Registros</th>
                                        <th style="width:44%;">Debe estandarizarse como</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filas as $fila)
                                        <tr>
                                            <td style="font-family:ui-monospace,Menlo,monospace;font-size:.86rem;">
                                                {{ $fila['texto'] }}
                                                <input type="hidden" name="catalogo[{{ $indice }}]" value="{{ $catalogo }}">
                                                <input type="hidden" name="texto_crudo[{{ $indice }}]" value="{{ $fila['texto'] }}">
                                            </td>
                                            <td class="text-center">
                                                <span class="r-chip r-chip--neut"><b>{{ number_format($fila['registros']) }}</b></span>
                                            </td>
                                            <td>
                                                <select name="valor[{{ $indice }}]" class="form-control form-control-sm">
                                                    <option value="">— Dejar pendiente —</option>
                                                    @foreach($valores[$catalogo] as $opcion)
                                                        <option value="{{ $opcion }}">{{ $opcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        @php $indice++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($indice > 0)
            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="r-btn-guardar">
                    <i class="fas fa-save mr-1"></i> Guardar las equivalencias marcadas
                </button>
            </div>
        @endif
    </form>

@stop
