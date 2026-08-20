@extends('adminlte::page')

@section('title', 'Diagnósticos (CIE-10)')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-notes-medical"></i> Diagnósticos (CIE-10)</h1>
        <a href="{{ route('diagnosticos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo diagnóstico
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Catálogo de diagnósticos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="diagnosticos-table" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="120px">Código</th>
                            <th>Descripción</th>
                            <th width="150px">Creado</th>
                            <th width="130px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            var table = $('#diagnosticos-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('diagnosticos.index') }}",
                    type: 'GET',
                    error: function (xhr, error, thrown) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar los datos: ' + thrown });
                    }
                },
                columns: [
                    { data: 'codigo', name: 'codigo', className: 'text-center font-weight-bold' },
                    { data: 'descripcion', name: 'descripcion' },
                    { data: 'created_at', name: 'created_at', className: 'text-center' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm', title: 'Diagnosticos_CIE10' },
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-info btn-sm', title: 'Diagnosticos_CIE10' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-secondary btn-sm', title: 'Diagnósticos CIE-10' }
                ]
            });

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var url = $(this).data('url');
                var descripcion = $(this).data('descripcion');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar el diagnóstico "${descripcion}"?`,
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
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({ icon: 'success', title: 'Eliminado', text: response.message, timer: 2000, showConfirmButton: false });
                                    table.ajax.reload();
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                                }
                            },
                            error: function (xhr) {
                                var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al eliminar el diagnóstico';
                                Swal.fire({ icon: 'error', title: 'Error', text: message });
                            }
                        });
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
