@extends('adminlte::page')

@section('title', 'Procedimientos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Procedimientos</h1>
        <div>
            <button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#modalImportarEpidemiologia">
                <i class="fas fa-vial mr-1"></i> 1. Importar Epidemiología
            </button>
            <button type="button"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#modalImportarTxt"
                    @if(($totalPacientes ?? 0) === 0) disabled title="Primero debe importar el archivo de epidemiología" @endif>
                <i class="fas fa-file-upload mr-1"></i> 2. Importar PROA (TXT/SQL)
            </button>
        </div>
    </div>
    @if(isset($totalPacientes))
        <div class="mt-2 small text-muted">
            <i class="fas fa-info-circle"></i>
            Pacientes registrados: <strong>{{ $totalPacientes }}</strong> ·
            Seguimientos microbiológicos: <strong>{{ $totalSeguimientos }}</strong>
            @if($totalPacientes === 0)
                <span class="text-danger ml-2">— el botón de PROA se habilitará después de cargar epidemiología.</span>
            @endif
        </div>
    @endif
@stop

@section('content')

    {{-- Modal 1: importación de Epidemiología (base madre) --}}
    <div class="modal fade" id="modalImportarEpidemiologia" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('seguimiento.importar.procesar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-vial mr-1"></i> Paso 1 · Importar archivo de Epidemiología
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">
                            Carga la <strong>base madre de epidemiología</strong> (pacientes, microorganismos y antibiogramas)
                            y, opcionalmente, el archivo <strong>PROA</strong> (procedimientos e intervención).
                            Formatos aceptados: <strong>Excel (.xlsx), CSV (.csv) o texto (.txt)</strong>.
                        </p>
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Si subes el libro de Excel de la plantilla, el sistema toma automáticamente la hoja
                            <strong>EPIDEMIOLOGIA</strong> y la hoja <strong>PROA</strong>. Recuerda borrar la fila de ejemplo (amarilla).
                        </div>

                        <div class="form-group">
                            <label for="archivo_epidemiologia">Archivo de Epidemiología <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input"
                                       id="archivo_epidemiologia"
                                       name="archivo_epidemiologia"
                                       accept=".txt,.csv,.xlsx,.ods"
                                       required>
                                <label class="custom-file-label" for="archivo_epidemiologia">Seleccionar archivo...</label>
                            </div>
                            <small class="form-text text-muted mt-1">TXT separado por tabulaciones, CSV, o Excel (hoja EPIDEMIOLOGIA).</small>
                        </div>

                        <div class="form-group">
                            <label for="archivo_proa">Archivo PROA <small class="text-muted">(opcional)</small></label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input"
                                       id="archivo_proa"
                                       name="archivo_proa"
                                       accept=".txt,.csv,.xlsx,.ods">
                                <label class="custom-file-label" for="archivo_proa">Seleccionar archivo...</label>
                            </div>
                            <small class="form-text text-muted mt-1">TXT separado por |, CSV, o Excel (hoja PROA).</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-upload mr-1"></i> Procesar archivos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 2: importación de PROA / procedimientos --}}
    <div class="modal fade" id="modalImportarTxt" tabindex="-1" role="dialog" aria-labelledby="modalImportarTxtLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('procedimientos.procesar-archivo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImportarTxtLabel">
                            <i class="fas fa-file-import mr-1"></i> Paso 2 · Importar datos PROA / procedimientos
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if(($totalPacientes ?? 0) === 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Aún no hay pacientes en la base. Importe primero el archivo de epidemiología.
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="nom_procedimiento">Nombre del procedimiento <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nom_procedimiento') is-invalid @enderror"
                                   id="nom_procedimiento"
                                   name="nom_procedimiento"
                                   placeholder="Ej: Antibióticos agosto 2025"
                                   value="{{ old('nom_procedimiento') }}"
                                   @if(($totalPacientes ?? 0) === 0) disabled @endif>
                            @error('nom_procedimiento')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="archivo_txt">Archivo de datos <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('archivo_txt') is-invalid @enderror"
                                       id="archivo_txt"
                                       name="archivo_txt"
                                       accept=".txt,.sql"
                                       @if(($totalPacientes ?? 0) === 0) disabled @endif>
                                <label class="custom-file-label" for="archivo_txt">Seleccionar archivo (.txt o .sql)...</label>
                            </div>
                            @error('archivo_txt')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted mt-1">
                                Formatos soportados: <strong>.txt</strong> (datos separados por <code>|</code>) o <strong>.sql</strong> (sentencias INSERT INTO).
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit"
                                class="btn btn-primary"
                                id="btnImportar"
                                @if(($totalPacientes ?? 0) === 0) disabled @endif>
                            <i class="fas fa-upload mr-1"></i> Importar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ================================================================= --}}
    {{-- Bases de datos cargadas: vaciado rápido (solo administradores)   --}}
    {{-- ================================================================= --}}
    @can('admin')
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-vial mr-1"></i> Base de Epidemiología</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">Pacientes: <strong>{{ $totalPacientes }}</strong></p>
                        <p class="mb-3">Seguimientos microbiológicos: <strong>{{ $totalSeguimientos }}</strong></p>
                        <form action="{{ route('epidemiologia.vaciar') }}" method="POST" class="form-vaciar"
                              data-que="Epidemiología"
                              data-detalle="{{ $totalSeguimientos }} seguimiento(s) y {{ $totalPacientes }} paciente(s)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    @if($totalSeguimientos === 0 && $totalPacientes === 0) disabled title="No hay datos que eliminar" @endif>
                                <i class="fas fa-trash mr-1"></i> Vaciar epidemiología
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-outline card-primary h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-capsules mr-1"></i> Base de PROA</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">Detalles de procedimiento: <strong>{{ $totalDetallesProa }}</strong></p>
                        <p class="mb-3">Intervenciones PROA: <strong>{{ $totalIntervenciones }}</strong></p>
                        <form action="{{ route('proa.vaciar') }}" method="POST" class="form-vaciar"
                              data-que="PROA"
                              data-detalle="{{ $totalDetallesProa }} detalle(s) y {{ $totalIntervenciones }} intervención(es)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    @if($totalDetallesProa === 0 && $totalIntervenciones === 0) disabled title="No hay datos que eliminar" @endif>
                                <i class="fas fa-trash mr-1"></i> Vaciar PROA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Procedimiento</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procedimientos as $procedimiento)
                            <tr>
                                <td>{{ $procedimiento->id_procedimiento }}</td>
                                <td>{{ $procedimiento->Nom_procedimiento }}</td>
                                <td>{{ $procedimiento->fecha_procedimiento ? $procedimiento->fecha_procedimiento->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('procedimientos.show', $procedimiento->id_procedimiento) }}"
                                       class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('procedimientos.destroy', $procedimiento->id_procedimiento) }}"
                                          method="POST" class="d-inline form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay procedimientos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $procedimientos->links() }}
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            // Actualizar el label del input de archivo
            $('#archivo_txt').on('change', function () {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo (.txt o .sql)...');
            });

            // Inputs de la importación de epidemiología / PROA
            $('#archivo_epidemiologia, #archivo_proa').on('change', function () {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Seleccionar archivo...');
            });

            // Mostrar spinner al enviar el formulario de importar
            $('form[action="{{ route('procedimientos.procesar-archivo') }}"]').on('submit', function () {
                $('#btnImportar').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm mr-1" role="status"></span> Procesando...'
                );
            });

            // Vaciar una base completa: exige escribir ELIMINAR
            $('.form-vaciar').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                var que = $(form).data('que');
                var detalle = $(form).data('detalle');

                Swal.fire({
                    title: '¿Vaciar la base de ' + que + '?',
                    html: 'Se eliminarán <strong>' + detalle + '</strong>.<br>' +
                          'Esta acción <strong>no se puede deshacer</strong>.<br><br>' +
                          'Escribe <code>ELIMINAR</code> para confirmar:',
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'ELIMINAR',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Sí, vaciar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: function (valor) {
                        if (valor !== 'ELIMINAR') {
                            Swal.showValidationMessage('Debes escribir exactamente ELIMINAR');
                            return false;
                        }
                        return true;
                    }
                }).then(function (r) {
                    if (r.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Confirmar eliminación con SweetAlert
            $('.form-eliminar').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: '¿Eliminar procedimiento?',
                    text: 'Se eliminarán también todos sus registros de pacientes. Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Si hay errores de validación, reabrir el modal automáticamente
            @if ($errors->any())
                $('#modalImportarTxt').modal('show');
            @endif
        });
    </script>
@stop