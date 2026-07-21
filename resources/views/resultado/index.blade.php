@extends('adminlte::page')

@section('title', 'Resultado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-clipboard-list"></i> Resultado</h1>
        <a href="{{ route('resultado.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Resultado
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Resultados</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="resultado-table" class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Fecha Creación</th>
                            <th>Última Actualización</th>
                            <th width="120px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargan vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .table th {
            background-color: #343a40;
            color: white;
            border-color: #454d55;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Configuración de DataTable
            var table = $('#resultado-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('resultado.index') }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('Error en AJAX:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar los datos: ' + thrown
                        });
                    }
                },
                columns: [
                    { data: 'id', name: 'id', className: 'text-center' },
                    { data: 'descripcion', name: 'descripcion' },
                    { data: 'created_at', name: 'created_at', className: 'text-center' },
                    { data: 'updated_at', name: 'updated_at', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Resultados_' + new Date().toISOString().slice(0,10)
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Resultados',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        title: 'Resultados_' + new Date().toISOString().slice(0,10)
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm',
                        title: 'Resultados'
                    }
                ]
            });

            // Manejar eliminación con confirmación
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var descripcion = $(this).data('descripcion');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el resultado "${descripcion}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Eliminado',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = 'Error al eliminar el resultado';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Mostrar mensajes de éxito/error desde el servidor
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@stop
