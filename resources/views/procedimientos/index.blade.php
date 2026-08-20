@extends('adminlte::page')

@section('title', 'Procedimientos')

@section('content_top_nav_right')
    @include('partials.notificaciones-bell')
@stop

@section('content_header')
    <div class="proc-head">
        <div>
            <h1 class="proc-title"><i class="fas fa-notes-medical mr-2"></i>Procedimientos</h1>
            <p class="proc-sub">Importación de bases y listado de procedimientos cargados.</p>
        </div>
        <div class="proc-head-actions">
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalImportarEpidemiologia">
                <i class="fas fa-vial mr-1"></i> 1 · Epidemiología
            </button>
            <button type="button"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#modalImportarTxt"
                    @if(($totalPacientes ?? 0) === 0) disabled title="Primero debe importar el archivo de epidemiología" @endif>
                <i class="fas fa-file-upload mr-1"></i> 2 · PROA
            </button>
        </div>
    </div>
@stop

@section('content')

    <div class="proc-bento">

        {{-- ── Alertas ──────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div class="proc-cell proc-cell--full">
                <div class="proc-flash proc-flash--ok">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="proc-cell proc-cell--full">
                <div class="proc-flash proc-flash--err">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="proc-cell proc-cell--full">
                <div class="proc-flash proc-flash--err">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Estado de las bases ──────────────────────────────────────── --}}
        <div class="proc-cell proc-cell--half">
            <div class="proc-card proc-card--epi">
                <div class="proc-card-head">
                    <span class="proc-ico proc-ico--epi"><i class="fas fa-vial"></i></span>
                    <div>
                        <h3>Base de Epidemiología</h3>
                        <p class="proc-card-sub">Pacientes, microorganismos y antibiogramas.</p>
                    </div>
                </div>

                <div class="proc-stats">
                    <div class="proc-stat">
                        <span class="proc-stat-num">{{ $totalPacientes }}</span>
                        <span class="proc-stat-lbl">Pacientes</span>
                    </div>
                    <div class="proc-stat">
                        <span class="proc-stat-num">{{ $totalSeguimientos }}</span>
                        <span class="proc-stat-lbl">Seguimientos</span>
                    </div>
                </div>

                @if(($totalPacientes ?? 0) === 0)
                    <p class="proc-hint"><i class="fas fa-info-circle mr-1"></i>
                        Importa esta base primero: PROA se cruza contra ella.</p>
                @endif

                @can('admin')
                    <div class="proc-card-foot">
                        <form action="{{ route('epidemiologia.vaciar') }}" method="POST" class="form-vaciar"
                              data-que="Epidemiología"
                              data-detalle="{{ $totalSeguimientos }} seguimiento(s) y {{ $totalPacientes }} paciente(s)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    @if($totalSeguimientos === 0 && $totalPacientes === 0) disabled title="No hay datos que eliminar" @endif>
                                <i class="fas fa-trash mr-1"></i> Vaciar epidemiología
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>

        <div class="proc-cell proc-cell--half">
            <div class="proc-card proc-card--proa">
                <div class="proc-card-head">
                    <span class="proc-ico proc-ico--proa"><i class="fas fa-capsules"></i></span>
                    <div>
                        <h3>Base de PROA</h3>
                        <p class="proc-card-sub">Antimicrobianos e intervenciones registradas.</p>
                    </div>
                </div>

                <div class="proc-stats">
                    <div class="proc-stat">
                        <span class="proc-stat-num">{{ $totalDetallesProa }}</span>
                        <span class="proc-stat-lbl">Detalles</span>
                    </div>
                    <div class="proc-stat">
                        <span class="proc-stat-num">{{ $totalIntervenciones }}</span>
                        <span class="proc-stat-lbl">Intervenciones</span>
                    </div>
                </div>

                @can('admin')
                    <div class="proc-card-foot">
                        <form action="{{ route('proa.vaciar') }}" method="POST" class="form-vaciar"
                              data-que="PROA"
                              data-detalle="{{ $totalDetallesProa }} detalle(s) y {{ $totalIntervenciones }} intervención(es)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    @if($totalDetallesProa === 0 && $totalIntervenciones === 0) disabled title="No hay datos que eliminar" @endif>
                                <i class="fas fa-trash mr-1"></i> Vaciar PROA
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>

        {{-- ── Listado de procedimientos ────────────────────────────────── --}}
        <div class="proc-cell proc-cell--full">
            <div class="proc-card proc-card--tabla">
                <div class="proc-card-head">
                    <span class="proc-ico"><i class="fas fa-list-ul"></i></span>
                    <div>
                        <h3>Procedimientos cargados</h3>
                        <p class="proc-card-sub">Cada importación de PROA genera un procedimiento.</p>
                    </div>
                    <span class="proc-badge">{{ $procedimientos->total() }}</span>
                </div>

                <div class="proc-tabla-wrap">
                    <table class="proc-tabla">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Nombre del procedimiento</th>
                                <th style="width: 190px;">Fecha</th>
                                <th style="width: 120px;" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($procedimientos as $procedimiento)
                                <tr>
                                    <td class="proc-id">#{{ $procedimiento->id_procedimiento }}</td>
                                    <td class="proc-nombre">{{ $procedimiento->Nom_procedimiento }}</td>
                                    <td class="proc-fecha">
                                        {{ $procedimiento->fecha_procedimiento
                                            ? $procedimiento->fecha_procedimiento->format('d/m/Y H:i')
                                            : '—' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('procedimientos.show', $procedimiento->id_procedimiento) }}"
                                           class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('procedimientos.destroy', $procedimiento->id_procedimiento) }}"
                                              method="POST" class="d-inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="proc-vacio">
                                            <i class="fas fa-inbox"></i>
                                            <p>No hay procedimientos registrados todavía.</p>
                                            <small>Importa un archivo PROA para crear el primero.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($procedimientos->hasPages())
                    <div class="proc-paginacion">
                        {{ $procedimientos->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ================================================================= --}}
    {{-- Modales de importación                                            --}}
    {{-- ================================================================= --}}

    {{-- Modal 1: importación de Epidemiología (base madre) --}}
    <div class="modal fade" id="modalImportarEpidemiologia" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('seguimiento.importar.procesar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-vial mr-1"></i> Paso 1 · Importar Epidemiología
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">
                            Carga la <strong>base madre de epidemiología</strong> (pacientes, microorganismos y antibiogramas)
                            y, opcionalmente, el archivo <strong>PROA</strong>.
                            Formatos: <strong>.xlsx, .ods, .csv o .txt</strong>.
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
                        <button type="submit" class="btn btn-primary">
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
                            <i class="fas fa-file-import mr-1"></i> Paso 2 · Importar PROA / procedimientos
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

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* ── Encabezado ─────────────────────────────────────────────── */
    .proc-head {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 12px;
    }
    .proc-title { font-size: 1.6rem; font-weight: 700; color: #262b34; margin: 0; }
    .proc-sub   { margin: .3rem 0 0; color: #6b7280; font-size: .9rem; }
    .proc-head-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    /* ── Bento grid ─────────────────────────────────────────────── */
    .proc-bento {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        align-items: start;
    }
    .proc-cell--full { grid-column: 1 / -1; }
    .proc-cell--half { grid-column: span 1; }

    @media (max-width: 991.98px) {
        .proc-bento { grid-template-columns: 1fr; }
        .proc-cell--half { grid-column: 1 / -1; }
    }

    /* ── Tarjetas ───────────────────────────────────────────────── */
    .proc-card {
        background: rgba(255, 255, 255, 0.97);
        border: 1px solid #e5e7f0;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: none;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .proc-card--epi  { border-top: 3px solid #2a377e; }
    .proc-card--proa { border-top: 3px solid #2e7d5b; }

    .proc-card-head {
        display: flex; align-items: flex-start; gap: 12px; margin-bottom: 18px;
    }
    .proc-card-head h3 {
        font-size: 1rem; font-weight: 700; color: #262b34; margin: 0;
    }
    .proc-card-sub {
        margin: .25rem 0 0; font-size: .84rem; color: #6b7280; line-height: 1.45;
    }
    .proc-ico {
        flex: 0 0 auto;
        width: 38px; height: 38px; border-radius: 11px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #eef1fa; color: #2a377e; font-size: 1rem;
    }
    .proc-ico--proa { background: #e9f3ee; color: #2e7d5b; }
    .proc-badge {
        margin-left: auto;
        background: #eef1fa; color: #2a377e;
        border-radius: 999px; padding: 4px 13px;
        font-size: .8rem; font-weight: 700;
    }

    /* ── Estadísticas ───────────────────────────────────────────── */
    .proc-stats {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;
    }
    .proc-stat {
        background: #f7f8fb; border: 1px solid #edeff6;
        border-radius: 12px; padding: 14px 16px;
    }
    .proc-stat-num {
        display: block; font-size: 1.7rem; font-weight: 700;
        color: #262b34; line-height: 1.1;
    }
    .proc-stat-lbl {
        display: block; margin-top: 3px;
        font-size: .72rem; text-transform: uppercase; letter-spacing: .05em;
        color: #8b93a5; font-weight: 600;
    }
    .proc-hint {
        margin: 14px 0 0; font-size: .82rem; color: #8a6d3b;
        background: #fdf6e9; border: 1px solid #f2e3c4;
        border-radius: 10px; padding: 9px 12px;
    }
    .proc-card-foot {
        margin-top: auto; padding-top: 18px;
        border-top: 1px solid #eef0f5;
    }
    .proc-card-foot form { margin: 0; }

    /* ── Tabla ──────────────────────────────────────────────────── */
    .proc-tabla-wrap { overflow-x: auto; }
    .proc-tabla {
        width: 100%; border-collapse: separate; border-spacing: 0;
    }
    .proc-tabla thead th {
        background: #f5f6fa;
        color: #5a6172;
        font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        padding: 11px 14px;
        border-bottom: 1px solid #e5e7f0;
        white-space: nowrap;
    }
    .proc-tabla thead th:first-child { border-radius: 10px 0 0 0; }
    .proc-tabla thead th:last-child  { border-radius: 0 10px 0 0; }
    .proc-tabla tbody td {
        padding: 13px 14px;
        border-bottom: 1px solid #f0f2f7;
        font-size: .88rem; color: #454b56;
        vertical-align: middle;
    }
    .proc-tabla tbody tr:last-child td { border-bottom: 0; }
    .proc-tabla tbody tr { transition: background-color .15s ease; }
    .proc-tabla tbody tr:hover { background: #f9fafd; }
    .proc-id     { color: #8b93a5; font-variant-numeric: tabular-nums; }
    .proc-nombre { font-weight: 600; color: #262b34; }
    .proc-fecha  { color: #6b7280; font-variant-numeric: tabular-nums; white-space: nowrap; }

    .proc-vacio {
        text-align: center; padding: 42px 20px; color: #8b93a5;
    }
    .proc-vacio i { font-size: 2.1rem; opacity: .45; }
    .proc-vacio p { margin: 12px 0 2px; font-weight: 600; color: #6b7280; }
    .proc-vacio small { font-size: .82rem; }

    .proc-paginacion {
        display: flex; justify-content: center;
        margin-top: 18px; padding-top: 16px;
        border-top: 1px solid #eef0f5;
    }
    .proc-paginacion .pagination { margin: 0; }

    /* ── Flashes ────────────────────────────────────────────────── */
    .proc-flash {
        display: flex; align-items: flex-start; gap: 10px;
        border-radius: 14px; padding: 14px 18px;
        font-size: .9rem; border: 1px solid transparent;
    }
    .proc-flash i { font-size: 1.05rem; margin-top: 1px; }
    .proc-flash--ok  { background: #edf7f1; border-color: #cfe8db; color: #1f6146; }
    .proc-flash--err { background: #fdeeee; border-color: #f3cdcd; color: #96322f; }

    /* ── Modales ────────────────────────────────────────────────── */
    .modal-content { border: 0; border-radius: 16px; overflow: hidden; }
    .modal-header {
        background: #2a377e; color: #fff; border-bottom: 0; padding: 16px 22px;
    }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; }
    .modal-header .close { color: #fff; opacity: .8; text-shadow: none; }
    .modal-header .close:hover { opacity: 1; }
    .modal-body   { padding: 22px; }
    .modal-footer { border-top: 1px solid #eef0f5; padding: 14px 22px; }
</style>
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
