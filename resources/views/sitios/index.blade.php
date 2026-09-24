@extends('adminlte::page')

@section('title', 'Sitios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-crosshairs mr-2"></i>Sitios</h1>
        <a href="{{ route('sitios.create') }}" class="r-btn">
            <i class="fas fa-plus mr-1"></i> Nuevo sitio
        </a>
    </div>
@stop

@section('content')

    @include('sitios.partials.avisos')

    {{-- Términos nuevos que llegaron por importación y no están en el catálogo --}}
    @include('partials.aviso-pendientes', ['catalogo' => 'sitio'])

    <div class="r-ctx" style="position:static;">
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta">Catálogo estandarizado</span>
            <h3>{{ $sitios->total() }} {{ $sitios->total() === 1 ? 'sitio' : 'sitios' }}</h3>
        </div>
        <div class="r-ctx-nums">
            <a href="{{ route('equivalencias.index', ['catalogo' => 'sitio']) }}" class="r-chip r-chip--info" style="text-decoration:none;">
                <i class="fas fa-exchange-alt mr-1"></i> Ver equivalencias
            </a>
        </div>
    </div>

    <form method="GET" class="r-buscar">
        <label class="r-buscar-caja">
            <i class="fas fa-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar sitio…">
        </label>
        <button type="submit" class="r-btn">Buscar</button>
        @if(request('q'))
            <a href="{{ route('sitios.index') }}" class="r-btn-tenue" style="color:#6b7280;">Limpiar</a>
        @endif
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nombre estandarizado</th>
                        <th style="width:170px;" class="text-center">Textos que lo alimentan</th>
                        <th style="width:150px;" class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sitios as $sitio)
                        <tr>
                            <td class="text-muted">{{ $sitio->id }}</td>
                            <td><strong>{{ $sitio->nombre }}</strong></td>
                            <td class="text-center">
                                @if($sitio->equivalencias_count > 0)
                                    <a href="{{ route('equivalencias.index', ['catalogo' => 'sitio', 'q' => $sitio->nombre]) }}"
                                       class="r-chip r-chip--neut" style="text-decoration:none;">
                                        <b>{{ $sitio->equivalencias_count }}</b>
                                    </a>
                                @else
                                    <span class="text-muted" title="Todavía no llega ninguna variante con otro nombre">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('sitios.show', $sitio) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sitios.edit', $sitio) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('sitios.destroy', $sitio) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar el sitio «{{ $sitio->nombre }}»?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay sitios que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $sitios->links() }}</div>

@stop
