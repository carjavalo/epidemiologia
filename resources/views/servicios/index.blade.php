@extends('adminlte::page')

@section('title', 'Servicios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 style="font-size:1.6rem;margin-bottom:0;"><i class="fas fa-hospital-alt mr-2"></i>Servicios</h1>
        <a href="{{ route('servicios.create') }}" class="r-btn">
            <i class="fas fa-plus mr-1"></i> Nuevo servicio
        </a>
    </div>
@stop

@section('content')

    @include('servicios.partials.avisos')

    <div class="r-ctx" style="position:static;">
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta">Catálogo estandarizado</span>
            <h3>{{ $servicios->total() }} {{ $servicios->total() === 1 ? 'servicio' : 'servicios' }}</h3>
        </div>
        <div class="r-ctx-nums">
            <a href="{{ route('equivalencias.index', ['catalogo' => 'servicio']) }}" class="r-chip r-chip--info" style="text-decoration:none;">
                <i class="fas fa-exchange-alt mr-1"></i> Ver equivalencias
            </a>
        </div>
    </div>

    <form method="GET" class="r-buscar">
        <label class="r-buscar-caja">
            <i class="fas fa-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar servicio…">
        </label>
        <button type="submit" class="r-btn">Buscar</button>
        @if(request('q'))
            <a href="{{ route('servicios.index') }}" class="r-btn-tenue" style="color:#6b7280;">Limpiar</a>
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
                    @forelse($servicios as $servicio)
                        <tr>
                            <td class="text-muted">{{ $servicio->id }}</td>
                            <td><strong>{{ $servicio->nombre }}</strong></td>
                            <td class="text-center">
                                @if($servicio->equivalencias_count > 0)
                                    <a href="{{ route('equivalencias.index', ['catalogo' => 'servicio', 'q' => $servicio->nombre]) }}"
                                       class="r-chip r-chip--neut" style="text-decoration:none;">
                                        <b>{{ $servicio->equivalencias_count }}</b>
                                    </a>
                                @else
                                    <span class="r-chip r-chip--pend">sin equivalencias</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('servicios.destroy', $servicio) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar el servicio «{{ $servicio->nombre }}»?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay servicios que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $servicios->links() }}</div>

@stop
