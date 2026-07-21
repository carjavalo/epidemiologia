@extends('adminlte::page')

@section('title', 'Detalles de Categoría Quirúrgica')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-procedures"></i> Detalles de Categoría Quirúrgica</h1>
        <a href="{{ route('categoria-quirurgica.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Información de la Categoría Quirúrgica #{{ $categoriaQuirurgica->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th width="30%" class="bg-light"><i class="fas fa-hashtag"></i> ID</th>
                                        <td>{{ $categoriaQuirurgica->id }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><i class="fas fa-notes-medical"></i> Descripción</th>
                                        <td><span class="badge badge-primary badge-lg p-2">{{ $categoriaQuirurgica->descripcion }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><i class="fas fa-calendar-plus"></i> Fecha de Creación</th>
                                        <td><span class="text-muted">{{ $categoriaQuirurgica->created_at->format('d/m/Y H:i:s') }} <small>({{ $categoriaQuirurgica->created_at->diffForHumans() }})</small></span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><i class="fas fa-calendar-edit"></i> Última Actualización</th>
                                        <td><span class="text-muted">{{ $categoriaQuirurgica->updated_at->format('d/m/Y H:i:s') }} <small>({{ $categoriaQuirurgica->updated_at->diffForHumans() }})</small></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Información:</strong> Esta categoría quirúrgica está registrada en el sistema ProAHUV
                        y alimenta el campo "Categoría Quirúrgica" del formulario de datos complementarios.
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categoria-quirurgica.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Volver al Listado
                        </a>
                        <div>
                            <a href="{{ route('categoria-quirurgica.edit', $categoriaQuirurgica) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-delete"
                                    data-url="{{ route('categoria-quirurgica.destroy', $categoriaQuirurgica) }}"
                                    data-descripcion="{{ $categoriaQuirurgica->descripcion }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
        .table th { background-color: #f8f9fa !important; border-color: #dee2e6; font-weight: 600; }
        .badge-lg { font-size: 1rem; padding: 0.5rem 1rem; }
        .alert-info { border-left: 4px solid #17a2b8; }
        .btn-group .btn { margin-left: 5px; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var descripcion = $(this).data('descripcion');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la categoría quirúrgica "${descripcion}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('<form>', { 'method': 'POST', 'action': url });
                        form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
                        form.append($('<input>', { 'type': 'hidden', 'name': '_method', 'value': 'DELETE' }));
                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Éxito', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}' });
            @endif
        });
    </script>
@stop
