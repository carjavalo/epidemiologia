@extends('adminlte::page')

@section('title', 'Perfil Antimicrobiano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-shield-virus"></i> Perfil Antimicrobiano</h1>
        <a href="{{ route('pantimicrobiano.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Perfil Antimicrobiano
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Perfiles Antimicrobianos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="pantimicrobianos-table" class="table table-bordered table-striped table-hover">
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
            margin: 0 2px;
        }
        .table-responsive {
            border-radius: 0.375rem;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }
        .dt-buttons {
            margin-bottom: 1rem;
        }
        .dt-button {
            margin-right: 0.5rem !important;
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
            const table = $('#pantimicrobianos-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('pantimicrobiano.index') }}",
                    type: 'GET'
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
                        title: 'Perfiles Antimicrobianos'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        title: 'Perfiles Antimicrobianos',
                        orientation: 'landscape'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info btn-sm',
                        title: 'Perfiles Antimicrobianos'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-secondary btn-sm',
                        title: 'Perfiles Antimicrobianos'
                    }
                ]
            });

            // Función para eliminar registro
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const descripcion = $(this).data('descripcion');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el perfil antimicrobiano "${descripcion}"?`,
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
                                    Swal.fire('Eliminado', response.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Error al eliminar el registro', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
