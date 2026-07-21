@extends('adminlte::page')

@section('title', 'Sistema Internacional')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-globe"></i> Sistema Internacional</h1>
        <a href="{{ route('sis-internacional.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Sistema
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Sistemas Internacionales</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="sistemas-table" class="table table-bordered table-striped table-hover">
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
                        <!-- Los datos se cargan via AJAX -->
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
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Configuración del DataTable
            const table = $('#sistemas-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("sis-internacional.index") }}',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('Error en AJAX:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al cargar los datos. Por favor, recarga la página.'
                        });
                    }
                },
                columns: [
                    { data: 'id', name: 'id', width: '60px', className: 'text-center' },
                    { data: 'descripcion', name: 'descripcion' },
                    { data: 'created_at', name: 'created_at', width: '150px', className: 'text-center' },
                    { data: 'updated_at', name: 'updated_at', width: '150px', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '120px', className: 'text-center' }
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
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['10%', '50%', '20%', '20%'];
                            doc.styles.tableHeader.fontSize = 10;
                            doc.defaultStyle.fontSize = 9;
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    }
                ]
            });

            // Manejar eliminación
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const descripcion = $(this).data('descripcion');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el sistema "${descripcion}"?`,
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
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al eliminar el sistema internacional.'
                                });
                            }
                        });
                    }
                });
            });

            // Mostrar mensajes de éxito/error
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
