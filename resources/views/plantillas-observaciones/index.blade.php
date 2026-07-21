@extends('adminlte::page')

@section('title', 'Plantilla de Observaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Plantilla de Observaciones</h1>
        <a href="{{ route('plantillas-observaciones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Plantilla
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Plantillas de Observaciones</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="plantillas-observaciones-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Fecha Creación</th>
                            <th>Fecha Actualización</th>
                            <th>Acciones</th>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 4px;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 4px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configuración de DataTable
            const table = $('#plantillas-observaciones-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('plantillas-observaciones.index') }}",
                    type: 'GET',
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error, code);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'id', name: 'id', width: '80px' },
                    { data: 'descripcion', name: 'descripcion' },
                    { data: 'created_at', name: 'created_at', width: '150px' },
                    { data: 'updated_at', name: 'updated_at', width: '150px' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '120px' }
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

            // Manejar eliminación con confirmación
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const plantillaName = $(this).data('plantilla-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la plantilla de observación "${plantillaName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
