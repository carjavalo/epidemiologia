@extends('adminlte::page')

@section('title', 'Equivalencias')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-exchange-alt mr-2"></i>Equivalencias</h1>
        <a href="{{ route('equivalencias.create') }}" class="r-btn"><i class="fas fa-plus mr-1"></i> Nueva equivalencia</a>
    </div>
@stop

@section('content')

    @include('equivalencias.partials.avisos')

    <div class="r-ctx" style="position:static;">
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta">Texto de la fuente &rarr; valor estandarizado</span>
            <h3>{{ number_format($equivalencias->total()) }} equivalencias</h3>
        </div>
        <div class="r-ctx-nums">
            <a href="{{ route('equivalencias.pendientes') }}"
               class="r-chip {{ $totalPendientes ? 'r-chip--pend' : 'r-chip--ok' }}" style="text-decoration:none;">
                <i class="fas fa-triangle-exclamation mr-1"></i><b>{{ $totalPendientes }}</b>&nbsp;pendientes
            </a>
        </div>
    </div>

    <form method="GET" class="r-buscar">
        <label class="r-buscar-caja">
            <i class="fas fa-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por texto crudo o por valor…">
        </label>
        <select name="catalogo" class="r-select" onchange="this.form.submit()">
            <option value="">Todos los catálogos</option>
            @foreach($catalogos as $clave => $nombre)
                <option value="{{ $clave }}" @selected(request('catalogo') === $clave)>{{ $nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="r-btn">Buscar</button>
        @if(request('q') || request('catalogo'))
            <a href="{{ route('equivalencias.index') }}" class="r-btn-tenue" style="color:#6b7280;">Limpiar</a>
        @endif
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:150px;">Catálogo</th>
                        <th>Texto que llega de la fuente</th>
                        <th>Se estandariza como</th>
                        <th style="width:110px;" class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equivalencias as $eq)
                        <tr>
                            <td>
                                <span class="r-chip {{ $eq->catalogo === 'servicio' ? 'r-chip--info' : 'r-chip--neut' }}">
                                    {{ $eq->catalogo_nombre }}
                                </span>
                            </td>
                            <td style="font-family:ui-monospace,Menlo,monospace;font-size:.86rem;">{{ $eq->texto_crudo }}</td>
                            <td><strong>{{ $eq->valor }}</strong></td>
                            <td class="text-right">
                                <a href="{{ route('equivalencias.edit', $eq) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('equivalencias.destroy', $eq) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta equivalencia?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay equivalencias que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $equivalencias->links() }}</div>

@stop
