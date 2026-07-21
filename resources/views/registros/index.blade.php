@extends('adminlte::page')

@section('title', 'Registros por Servicio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: -10px;">
        <h1 style="font-size: 1.6rem; margin-bottom: 0;"><i class="fas fa-hospital mr-2"></i>Registros por Servicio</h1>
        <span class="badge badge-info" style="font-size:0.85rem">
            {{ $serviciosPaginados->total() }} servicio(s)
        </span>
    </div>
@stop

@section('content')

    @php
        // Devuelve la lista de opciones incluyendo el valor guardado si no está en ella.
        // Evita que un dato importado (p. ej. "12. Herida Quirurgica Organo/Espacio")
        // se pierda al guardar por no existir como <option>.
        $conValor = function (array $opciones, $valor) {
            $valor = trim((string) ($valor ?? ''));
            if ($valor !== '' && !in_array($valor, array_map('strval', $opciones), true)) {
                $opciones[] = $valor;
            }
            return $opciones;
        };
    @endphp

    {{-- Botón volver si hay servicio seleccionado --}}
    @if($servicioSeleccionado)
        <div class="mb-2">
            <a href="{{ route('registros.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left mr-1"></i>Volver a Servicios
            </a>
        </div>
    @endif

    {{-- Buscador --}}
    <div class="card card-outline card-primary mb-2">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('registros.index') }}">
                @if($servicioSeleccionado)
                    <input type="hidden" name="servicio" value="{{ $servicioSeleccionado }}">
                @endif
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por servicio, paciente o documento..."
                           value="{{ $search }}"
                           autofocus>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                        @if($search)
                            <a href="{{ route('registros.index', array_filter(['servicio' => $servicioSeleccionado, 'anio' => $anio, 'mes' => $mes])) }}" class="btn btn-outline-secondary" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Filtro por Año y Mes (según fecha de toma de muestra) --}}
                @php
                    $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
                @endphp
                <div class="form-row mt-2 align-items-center">
                    <div class="col-auto">
                        <span class="small font-weight-bold text-muted"><i class="far fa-calendar-alt mr-1"></i>Filtrar por fecha:</span>
                    </div>
                    <div class="col-auto">
                        <select name="anio" class="form-control form-control-sm" onchange="this.form.submit()" title="Año">
                            <option value="">Año: todos</option>
                            @foreach($aniosDisponibles as $a)
                                <option value="{{ $a }}" {{ (string) $anio === (string) $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="mes" class="form-control form-control-sm" onchange="this.form.submit()" title="Mes">
                            <option value="">Mes: todos</option>
                            @foreach($meses as $num => $nombre)
                                <option value="{{ $num }}" {{ (string) $mes === (string) $num ? 'selected' : '' }}>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                    </div>
                    @if($anio || $mes)
                        <div class="col-auto">
                            <a href="{{ route('registros.index', array_filter(['servicio' => $servicioSeleccionado, 'search' => $search])) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times mr-1"></i>Limpiar filtros
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- GRID PRINCIPAL --}}
    <div class="row {{ $servicioSeleccionado ? 'justify-content-center' : '' }}">
        @if(!$servicioSeleccionado)
            {{-- ============================================ --}}
            {{-- VISTA SERVICIOS: Grid de tarjetas de servicios --}}
            {{-- ============================================ --}}
            @forelse($serviciosPaginados as $servicio)
                @php
                    $servicioNombre = $servicio->ubicacion;
                    $pacientesPorServicio = $dataPorServicio[$servicioNombre] ?? collect();
                    $totalPacientes = $pacientesPorServicio->count();
                    $totalProaServicio = $proaCountPorServicio[$servicioNombre] ?? 0;
                @endphp

                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                    <div class="card card-outline card-primary service-card shadow-sm h-100">
                        <a href="{{ route('registros.index', ['servicio' => $servicioNombre]) }}" 
                           class="card-header service-header text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="service-icon mr-3">
                                        <i class="fas fa-hospital-alt"></i>
                                    </div>
                                    <div class="service-info">
                                        <h6 class="mb-0 font-weight-bold text-truncate" title="{{ $servicioNombre }}">
                                            {{ Str::limit($servicioNombre, 35) }}
                                        </h6>
                                        <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 6px;">
                                            <small class="text-light-muted">
                                                <i class="fas fa-users mr-1"></i>{{ $totalPacientes }} paciente(s)
                                            </small>
                                            @if($totalProaServicio > 0)
                                                <span class="badge badge-success badge-pill px-2 py-1" style="font-size: 0.72rem;" title="Pacientes con intervención PROA registrada">
                                                    <i class="fas fa-capsules mr-1"></i>{{ $totalProaServicio }} PROA
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-white-50 ml-2"></i>
                            </div>
                        </a>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>No se encontraron servicios.
                    </div>
                </div>
            @endforelse

        @else
            {{-- ============================================ --}}
            {{-- VISTA PACIENTES: Grid expandido de pacientes del servicio --}}
            {{-- ============================================ --}}
            @php
                $servicioActual = $serviciosPaginados->first();
                $servicioNombre = $servicioActual->ubicacion;
                $pacientesPorServicio = $dataPorServicio[$servicioNombre] ?? collect();
                $servicioKey = 'servicio-' . md5($servicioNombre);
                $totalProaServicioActual = $proaCountPorServicio[$servicioNombre] ?? 0;
            @endphp

            {{-- Info de paginación de pacientes --}}
            @if($pacientesPaginados)
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <span class="text-muted small">
                            <i class="fas fa-users mr-1"></i>
                            Mostrando {{ $pacientesPaginados->firstItem() }}–{{ $pacientesPaginados->lastItem() }}
                            de <strong>{{ $pacientesPaginados->total() }}</strong> pacientes
                        </span>
                        @if($totalProaServicioActual > 0)
                            <span class="badge badge-success badge-pill px-2 py-1" style="font-size: 0.78rem;" title="Pacientes con intervención PROA en este servicio">
                                <i class="fas fa-capsules mr-1"></i>{{ $totalProaServicioActual }} paciente(s) PROA
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="pacientes-scroll-container">
                    <div class="row justify-content-center">
                        @forelse($pacientesPorServicio as $pacienteId => $info)
                                @php
                                    $paciente             = $info['paciente'];
                                    $seguimiento          = $info['seguimiento'];
                                    $medicamentosPorPaciente = $info['medicamentos']; // collection|null
                                    $tienePROA            = $info['tiene_proa'];
                                    $primerRegistro       = $tienePROA ? $medicamentosPorPaciente->first()?->first() : null;
                                    $pacienteKey          = $servicioKey . '-pac-' . md5($pacienteId);
                                @endphp

                                <div class="col-xl-9 col-lg-10 col-md-12 mb-3">
                                    <div class="card patient-card shadow-lg">
                            <div class="card-header patient-header" data-toggle="collapse" data-target="#{{ $pacienteKey }}" role="button">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="patient-avatar mr-3">
                                            <i class="fas fa-user-injured text-primary"></i>
                                        </span>
                                        <div>
                                            <strong class="text-dark">{{ $paciente?->nombre ?? 'Sin nombre' }}</strong>
                                            <small class="d-block text-muted">
                                                ID {{ $paciente?->identificador_unico ?? '—' }} · HC {{ $paciente?->id_historia ?? '—' }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($tienePROA)
                                            <span class="badge badge-success badge-pill px-2 py-1 mr-2" style="font-size: 0.75rem;" title="Paciente con datos PROA">
                                                <i class="fas fa-capsules"></i> PROA
                                            </span>
                                            <span class="badge badge-info badge-pill px-2 py-1 mr-2" style="font-size: 0.8rem;">
                                                {{ $info['total_medic'] }} medicamento(s)
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-pill px-2 py-1 mr-2" style="font-size: 0.75rem;" title="Paciente solo en epidemiología">
                                                <i class="fas fa-vial"></i> Solo epidemiología
                                            </span>
                                        @endif
                                        <i class="fas fa-chevron-down collapse-icon text-muted" style="font-size: 1rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Contenido del paciente: Epidemiología y PROA --}}
                            <div id="{{ $pacienteKey }}" class="collapse">
                                <div class="card-body p-3">

                                    {{-- ========================================== --}}
                                    {{-- BLOQUE 1: EPIDEMIOLOGÍA --}}
                                    {{-- ========================================== --}}
                                    @php $epiKey = $pacienteKey . '-epi'; @endphp
                                    <div class="card mb-3 border-info">
                                        <div class="card-header bg-gradient-info text-white" 
                                             data-toggle="collapse" 
                                             data-target="#{{ $epiKey }}"
                                             role="button"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-chart-line mr-2"></i>
                                                    <strong style="font-size: 1rem;">EPIDEMIOLOGÍA</strong>
                                                    <span class="badge badge-light text-info ml-2" style="font-size: 0.8rem;" title="Registros de microorganismos del paciente">
                                                        <i class="fas fa-vial mr-1"></i>N. registros ({{ count($info['seguimientos']) }})
                                                    </span>
                                                    <small class="ml-2">(Datos básicos del paciente)</small>
                                                </div>
                                                <i class="fas fa-chevron-down collapse-icon"></i>
                                            </div>
                                        </div>
                                        <div id="{{ $epiKey }}" class="collapse">
                                            <div class="card-body bg-light p-3">
                                                {{-- Identidad del paciente (solo lectura, una sola vez) --}}
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="proa-label">Nombre</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" readonly
                                                               value="{{ $paciente?->nombre ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="proa-label">ID (Número Identificación)</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" readonly
                                                               value="{{ $paciente?->identificador_unico ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="proa-label">Fecha Nacimiento</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" readonly
                                                               value="{{ $paciente?->fecha_nacimiento ? $paciente->fecha_nacimiento->format('d/m/Y') : '' }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="proa-label">Sexo</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" readonly
                                                               value="{{ $paciente?->sexo ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="proa-label">Historia Clínica</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" readonly
                                                               value="{{ $paciente?->id_historia ?? '' }}">
                                                    </div>
                                                </div>

                                                {{-- Acordeón: un bloque por microorganismo. Los registros duplicados
                                                     del mismo microorganismo se agrupan dentro del mismo bloque. --}}
                                                @php
                                                    $gruposMicro = collect($info['seguimientos'])->groupBy(function ($r) {
                                                        $n = trim((string) ($r->microorganismo ?? ''));
                                                        return $n === '' ? '__SIN_MICROORGANISMO__' : mb_strtoupper($n, 'UTF-8');
                                                    });
                                                @endphp
                                                @foreach($gruposMicro as $microNombre => $grupoMicro)
                                                    @php
                                                        $microKey = $epiKey . '-m' . md5($microNombre);
                                                        // Registro representativo del grupo: sobre él se guardan los
                                                        // datos complementarios (que son iguales para todo el grupo).
                                                        $reg = $grupoMicro->first();
                                                    @endphp
                                                    <div class="card mb-2 border-secondary">
                                                        <div class="card-header bg-secondary text-white"
                                                             data-toggle="collapse"
                                                             data-target="#{{ $microKey }}"
                                                             role="button"
                                                             style="cursor: pointer;">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <i class="fas fa-vial mr-2"></i>
                                                                    <strong>Información adicional de {{ $reg->microorganismo ?: 'Sin microorganismo' }}</strong>
                                                                    <span class="badge badge-light text-dark ml-2" style="font-size:0.75rem;" title="Registros de este microorganismo">
                                                                        {{ $grupoMicro->count() }} registro(s)
                                                                    </span>
                                                                </div>
                                                                <i class="fas fa-chevron-down collapse-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div id="{{ $microKey }}" class="collapse {{ $loop->first ? 'show' : '' }}">
                                                            <div class="card-body bg-white p-3">
                                                                <form class="microorganismo-info-form" data-paciente="{{ $paciente?->identificador_unico }}">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ $reg->id }}">
                                                                    @php $bloqueadoInfo = !optional(auth()->user())->esAdmin() && $reg->edicion_bloqueada; @endphp
                                                                    @if($bloqueadoInfo)
                                                                        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:0.8rem;">
                                                                            <i class="fas fa-lock mr-1"></i> Este formulario ya fue registrado. Solo un administrador puede modificarlo.
                                                                        </div>
                                                                    @endif
                                                                    <fieldset @disabled($bloqueadoInfo)>

                                                    {{-- ── Datos de la muestra: se repiten una vez por cada registro
                                                         duplicado de este mismo microorganismo ── --}}
                                                    @foreach($grupoMicro as $fila)
                                                    <div class="registro-muestra">
                                                        <div class="registro-muestra-head">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       class="custom-control-input registro-check"
                                                                       id="{{ $microKey }}-chk-{{ $fila->id }}"
                                                                       name="registros_seleccionados[]"
                                                                       value="{{ $fila->id }}">
                                                                <label class="custom-control-label registro-muestra-tag"
                                                                       for="{{ $microKey }}-chk-{{ $fila->id }}">
                                                                    Registro #{{ $loop->iteration }}
                                                                </label>
                                                            </div>
                                                            @if($fila->fecha_toma_muestra)
                                                                <span class="registro-muestra-fecha">
                                                                    <i class="far fa-calendar-alt mr-1"></i>{{ $fila->fecha_toma_muestra->format('d/m/Y') }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Fila 2: Tipo de muestra, N. Reporte, Cultivo, Sede, Ubicación, Fecha toma muestra --}}
                                                        <div class="row mb-2">
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Tipo de Muestra</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][tipo_muestra]" class="form-control form-control-sm"
                                                                       value="{{ $fila->tipo_muestra ?? '' }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">N. Reporte</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][n_reporte]" class="form-control form-control-sm"
                                                                       value="{{ $fila->n_reporte ?? '' }}">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="proa-label">Cultivo</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][cultivo_num]" class="form-control form-control-sm"
                                                                       value="{{ $fila->cultivo_num ?? '' }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Sede</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][sede]" class="form-control form-control-sm"
                                                                       value="{{ $fila->sede ?? '' }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Ubicación</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][ubicacion]" class="form-control form-control-sm"
                                                                       value="{{ $fila->ubicacion ?? '' }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="proa-label">Fecha Toma de Muestra</label>
                                                                <input type="date" name="registros[{{ $fila->id }}][fecha_toma_muestra]"
                                                                       class="form-control form-control-sm fecha-muestra"
                                                                       value="{{ $fila->fecha_toma_muestra?->format('Y-m-d') }}">
                                                            </div>
                                                        </div>

                                                        {{-- Fila 3: Microorganismo, Sensibles, Intermedios, Resistentes, Marcadores --}}
                                                        <div class="row mb-2">
                                                            <div class="col-md-3">
                                                                <label class="proa-label">Microorganismo</label>
                                                                <input type="text" name="registros[{{ $fila->id }}][microorganismo]" class="form-control form-control-sm"
                                                                       value="{{ $fila->microorganismo ?? '' }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="proa-label">Sensibles</label>
                                                                <textarea name="registros[{{ $fila->id }}][sensibles]" class="form-control form-control-sm" rows="2">{{ $fila->sensibles ?? '' }}</textarea>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Intermedios</label>
                                                                <textarea name="registros[{{ $fila->id }}][intermedios]" class="form-control form-control-sm" rows="2">{{ $fila->intermedios ?? '' }}</textarea>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Resistentes</label>
                                                                <textarea name="registros[{{ $fila->id }}][resistentes]" class="form-control form-control-sm" rows="2">{{ $fila->resistentes ?? '' }}</textarea>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Marcadores</label>
                                                                <textarea name="registros[{{ $fila->id }}][marcadores_resistencia]" class="form-control form-control-sm" rows="2">{{ $fila->marcadores_resistencia ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>{{-- /.registro-muestra --}}

                                                    @unless($loop->last)
                                                        <hr class="registro-divider">
                                                    @endunless
                                                    @endforeach

                                                    {{-- ── Datos Complementarios de este microorganismo (se llenan
                                                         una sola vez y aplican a todos los registros del bloque) ── --}}
                                                    <hr class="my-3">

                                                    {{-- Fila 1: Datos constantes del paciente (se replican en todos los microorganismos) --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">País de origen</label>
                                                            <select name="pais_origen" class="form-control form-control-sm const-field">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($catalogos['paises'] as $pais)
                                                                    <option value="{{ $pais->nombre }}" {{ ($reg->pais_origen ?? '') == $pais->nombre ? 'selected' : '' }}>{{ $pais->nombre }}</option>
                                                                @endforeach
                                                                @if(!empty($reg->pais_origen) && !$catalogos['paises']->contains('nombre', $reg->pais_origen))
                                                                    <option value="{{ $reg->pais_origen }}" selected>{{ $reg->pais_origen }}</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="border rounded px-2 pt-1 pb-2 h-100 procedencia-box">
                                                                <div class="proa-label mb-1" style="font-weight: 600;">
                                                                    <i class="fas fa-map-marker-alt mr-1"></i>Procedencia
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <label class="proa-label">Departamento</label>
                                                                        {{-- Se llena vía API DANE (Divipola). Se conserva el valor guardado como opción por si la API no responde. --}}
                                                                        <select name="departamento" class="form-control form-control-sm dane-depto"
                                                                                data-selected="{{ $reg->departamento ?? '' }}">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @if(!empty($reg->departamento))
                                                                                <option value="{{ $reg->departamento }}" selected>{{ $reg->departamento }}</option>
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="proa-label">Municipio</label>
                                                                        <select name="municipio" class="form-control form-control-sm dane-mpio"
                                                                                data-selected="{{ $reg->municipio ?? '' }}">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @if(!empty($reg->municipio))
                                                                                <option value="{{ $reg->municipio }}" selected>{{ $reg->municipio }}</option>
                                                                            @endif
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="proa-label">Diagnóstico de ingreso</label>
                                                            <input type="text" name="diagnostico_ingreso" class="form-control form-control-sm const-field"
                                                                   value="{{ $reg->diagnostico_ingreso ?? '' }}">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 2: Tipo ID, Fecha de ingreso, Asegurador y Peso (constantes del paciente) --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Tipo ID</label>
                                                            <select name="tipo_id" class="form-control form-control-sm const-field">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['RC', 'TI', 'CC', 'CE', 'PA', 'PPT', 'CD', 'DNI'], $reg->tipo_id) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->tipo_id ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Fecha de ingreso</label>
                                                            <input type="date" name="fecha_ingreso_hosp" class="form-control form-control-sm const-field"
                                                                   value="{{ isset($reg->fecha_ingreso_hosp) ? $reg->fecha_ingreso_hosp->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="proa-label">Asegurador</label>
                                                            <input type="text" name="asegurador" class="form-control form-control-sm const-field"
                                                                   value="{{ $reg->asegurador ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Peso (kg)</label>
                                                            <input type="number" step="0.1" name="peso" class="form-control form-control-sm const-field"
                                                                   value="{{ $reg->peso ?? '' }}">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 3: Fecha quirurjica, Dias entre Qx e infeccion, Categoria quirurjica, Egreso --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Fecha quirúrgica previa a la infección</label>
                                                            <input type="date" name="fecha_quirurgica_previa" class="form-control form-control-sm"
                                                                   value="{{ isset($reg->fecha_quirurgica_previa) ? $reg->fecha_quirurgica_previa->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">DÍAS ENTRE Qx. PREVIA E INFECCIÓN</label>
                                                            <input type="text" name="dias_entre_qx_e_infeccion" class="form-control form-control-sm bg-white" readonly
                                                                   value="{{ $reg->dias_entre_qx_e_infeccion ?? '' }}" placeholder="Calculado automáticamente">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">CATEGORÍA Quirúrgica</label>
                                                            <select name="categoria_quirurgica" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($catalogos['categoriasQuirurgicas'] as $cat)
                                                                    <option value="{{ $cat->descripcion }}" {{ ($reg->categoria_quirurgica ?? '') == $cat->descripcion ? 'selected' : '' }}>{{ $cat->descripcion }}</option>
                                                                @endforeach
                                                                @if(!empty($reg->categoria_quirurgica) && !$catalogos['categoriasQuirurgicas']->contains('descripcion', $reg->categoria_quirurgica))
                                                                    <option value="{{ $reg->categoria_quirurgica }}" selected>{{ $reg->categoria_quirurgica }}</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Egreso</label>
                                                            <select name="egreso" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['VIVO', 'MUERTO', 'N/A'], $reg->egreso) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->egreso ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Fila 4: Sitio, TIPO, CLASIFICACIÓN, CLASIFICACIÓN EN TEXTO --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Sitio</label>
                                                            <select name="sitio" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['Info pendiente', '51. Infeccion Previa', '52. No cumple criterios'], $reg->sitio) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->sitio ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">TIPO</label>
                                                            <select name="tipo" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['1', '2'], $reg->tipo) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->tipo ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">CLASIFICACIÓN</label>
                                                            <select name="clasificacion" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['1', '2', '3', '4', '5', '6', '7'], $reg->clasificacion) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->clasificacion ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="proa-label">CLASIFICACIÓN EN TEXTO</label>
                                                            <input type="text" name="clasificacion_texto" class="form-control form-control-sm bg-white" readonly
                                                                   value="{{ $reg->clasificacion_texto ?? '' }}" placeholder="Calculado automáticamente">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 5: Especialidad que realizo cirugia, Procedimiento quirurjico, Tiempo quirurjico --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-4">
                                                            <label class="proa-label">Especialidad que realizó cirugía</label>
                                                            <select name="especialidad_cirugia" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['Cirugía General', 'Traumatología y Ortopedia', 'Urología', 'Ginecología y Obstetricia', 'Neurocirugía', 'Cirugía Cardiovascular', 'Cirugía Pediátrica', 'Cirugía Plástica', 'Oftalmología', 'Otorrinolaringología'], $reg->especialidad_cirugia) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->especialidad_cirugia ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="proa-label">Procedimiento quirúrgico</label>
                                                            <input type="text" name="procedimiento_quirurgico" class="form-control form-control-sm"
                                                                   value="{{ $reg->procedimiento_quirurgico ?? '' }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Tiempo Quirúrgico</label>
                                                            <input type="text" name="tiempo_quirurgico" class="form-control form-control-sm"
                                                                   value="{{ $reg->tiempo_quirurgico ?? '' }}" placeholder="Ej: 120 min">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 6: Baño quirurjico, ASEPSIA, PROFILAXIS, ANTIBIOTICOS USADOS, ASA, TIPO CIRUGÍA --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Baño quirúrgico</label>
                                                            <select name="bano_quirurgico" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->bano_quirurgico) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->bano_quirurgico ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Asepsia quirúrgica</label>
                                                            <select name="asepsia_quirurgica" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->asepsia_quirurgica) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->asepsia_quirurgica ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Profilaxis</label>
                                                            <select name="profilaxis" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->profilaxis) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->profilaxis ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Antibióticos usados</label>
                                                            <select name="antibioticos_usados" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->antibioticos_usados) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->antibioticos_usados ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">ASA Preoperatoria</label>
                                                            <select name="asa_preoperatoria" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->asa_preoperatoria) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->asa_preoperatoria ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Tipo Cirugía</label>
                                                            <select name="tipo_cirugia" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['ELECTIVA', 'URGENCIA'], $reg->tipo_cirugia) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->tipo_cirugia ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Fila 7: CLASIFICACIÓN CIRUGIA, NNIS, REVISIÓN CON EQUIPO, INTERCONSULTA --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Clasificación Cirugía</label>
                                                            <select name="clasificacion_cirugia" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['L (1)', 'LC (2)', 'C (3)', 'S (4)'], $reg->clasificacion_cirugia) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->clasificacion_cirugia ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Puntaje NNIS</label>
                                                            <select name="puntaje_nnis" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['0', '1', '2', '3', 'SD'], $reg->puntaje_nnis) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->puntaje_nnis ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Revisión con equipo</label>
                                                            <select name="revision_equipo" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['requiere apoyo', 'no requiere apoyo'], $reg->revision_equipo) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->revision_equipo ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="proa-label">Interconsulta con infectología</label>
                                                            <select name="interconsulta_infectologia" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['SI', 'NO'], $reg->interconsulta_infectologia) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->interconsulta_infectologia ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Fila 8: Fecha inserción, Fecha retiro, Comentarios --}}
                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Fecha de inserción</label>
                                                            <input type="date" name="fecha_insercion" class="form-control form-control-sm"
                                                                   value="{{ isset($reg->fecha_insercion) ? $reg->fecha_insercion->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Fecha de retiro</label>
                                                            <input type="date" name="fecha_retiro" class="form-control form-control-sm"
                                                                   value="{{ isset($reg->fecha_retiro) ? $reg->fecha_retiro->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="proa-label">Comentarios</label>
                                                            <textarea name="comentarios" class="form-control form-control-sm" rows="2" placeholder="Comentarios adicionales...">{{ $reg->comentarios ?? '' }}</textarea>
                                                        </div>
                                                    </div>

                                                                    </fieldset>

                                                    {{-- Botón guardar --}}
                                                    @unless($bloqueadoInfo)
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <span class="info-save-msg text-success mr-3 d-none">
                                                            <i class="fas fa-check-circle mr-1"></i> Registrado correctamente
                                                        </span>
                                                        <button type="button" class="btn btn-info btn-sm btn-registrar-info">
                                                            <i class="fas fa-save mr-1"></i> Registrar
                                                        </button>
                                                    </div>
                                                    @endunless

                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========================================== --}}
                                    {{-- BLOQUE 2: PROA (solo si el paciente está en el TXT/tabla de PROA) --}}
                                    {{-- ========================================== --}}
                                    @if($tienePROA)
                                    <div class="card mb-2 border-success">
                                        <div class="card-header bg-gradient-success text-white" 
                                             data-toggle="collapse" 
                                             data-target="#{{ $pacienteKey }}-proa"
                                             role="button"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-capsules mr-2"></i>
                                                    <strong style="font-size: 1rem;">PROA</strong>
                                                    <small class="ml-2">({{ $info['total_medic'] }} medicamento(s))</small>
                                                </div>
                                                <i class="fas fa-chevron-down collapse-icon"></i>
                                            </div>
                                        </div>
                                        <div id="{{ $pacienteKey }}-proa" class="collapse">
                                            <div class="card-body p-3 bg-light">

                                    {{-- Medicamentos del paciente --}}
                                    @foreach($medicamentosPorPaciente as $medicamento => $registros)
                                        @php
                                            $medKey = $pacienteKey . '-med-' . md5($medicamento);
                                            $totalDosis = $registros->count();
                                        @endphp

                                        {{-- Tarjeta de medicamento --}}
                                        <div class="card med-card mb-2">
                                            <div class="card-header med-header"
                                                 data-toggle="collapse"
                                                 data-target="#{{ $medKey }}"
                                                 aria-expanded="false"
                                                 role="button">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-pills text-success mr-1"></i>
                                                        <strong>{{ $medicamento }}</strong>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-secondary badge-sm mr-2">
                                                            {{ $totalDosis }} dosis
                                                        </span>
                                                        <i class="fas fa-chevron-down collapse-icon text-muted"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Registros individuales del medicamento --}}
                                            <div id="{{ $medKey }}" class="collapse">
                                                <div class="card-body p-2 bg-white">
                                                    @foreach($registros as $registro)
                                                        @php $regKey = $medKey . '-reg-' . $registro->id; @endphp

                                                        {{-- Tarjeta de registro individual --}}
                                                        <div class="card reg-card mb-2">
                                                            <div class="card-header reg-header"
                                                                 data-toggle="collapse"
                                                                 data-target="#{{ $regKey }}"
                                                                 aria-expanded="false"
                                                                 role="button">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <i class="fas fa-file-medical text-info mr-1"></i>
                                                                        <strong>{{ \Carbon\Carbon::parse($registro->Fec_Sumistro)->format('d/m/Y') }}</strong>
                                                                        <span class="text-muted ml-2">{{ $registro->Ho_Sumisnistro }}</span>
                                                                    </div>
                                                                    <i class="fas fa-chevron-down collapse-icon text-muted"></i>
                                                                </div>
                                                            </div>

                                                            {{-- Formulario PROA --}}
                                                            <div id="{{ $regKey }}" class="collapse">
                                                                <div class="card-body bg-white p-3">
                                                                @php $interv = $intervenciones[$registro->id] ?? null; @endphp

                                                                <form class="proa-form" data-id="{{ $registro->id }}">
                                                                @csrf

                                                                <input type="hidden" name="id_deta_procedimiento" value="{{ $registro->id }}">
                                                                @php $bloqueadoProa = !optional(auth()->user())->esAdmin() && optional($interv)->edicion_bloqueada; @endphp
                                                                @if($bloqueadoProa)
                                                                    <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:0.8rem;">
                                                                        <i class="fas fa-lock mr-1"></i> Esta intervención PROA ya fue registrada. Solo un administrador puede modificarla.
                                                                    </div>
                                                                @endif
                                                                <fieldset @disabled($bloqueadoProa)>

                                                                {{-- SECCIÓN: Datos del paciente (solo lectura, automáticos) --}}
                                                                <div class="section-title bg-primary text-white px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-user mr-1"></i> Datos del Paciente
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">MES</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                               name="mes" value="{{ $interv?->mes ?? ($registro->Fec_Sumistro ? \Carbon\Carbon::parse($registro->Fec_Sumistro)->format('m') : '') }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA DE INTERVENCIÓN</label>
                                                                        <input type="date" class="form-control form-control-sm"
                                                                               name="fecha_intervencion" value="{{ $interv?->fecha_intervencion?->format('Y-m-d') ?? date('Y-m-d') }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">SALA</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Nom_Sala ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label class="proa-label">CAMA</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Num_Cama ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA DE INGRESO</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->F_Ingreso ? \Carbon\Carbon::parse($registro->F_Ingreso)->format('d/m/Y') : '' }}">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label class="proa-label">ID EPS</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Cod_Eps ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">EPS</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Nom_Eps ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">HC</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Hist_Clinica ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label class="proa-label">TIPO ID</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Tipo_Ident ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">ID</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Num_Ident ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">NOMBRE</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Medico_Trata ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label class="proa-label">GÉNERO</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Sexo ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">CIE-10</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->CIE10 ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">DIAGNÓSTICOS</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               title="{{ $registro->Diagnostico }}"
                                                                               value="{{ Str::limit($registro->Diagnostico, 30) ?? '' }}">
                                                                    </div>
                                                                </div>

                                                                {{-- SECCIÓN: Antimicrobiano (automático) --}}
                                                                <div class="section-title bg-info text-white px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-pills mr-1"></i> Antimicrobiano
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA DE INICIO</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Fec_Sumistro ? \Carbon\Carbon::parse($registro->Fec_Sumistro)->format('d/m/Y') : '' }}">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">ANTIMICROBIANOS</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Antimicrobiano ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">VÍA DE ADMINISTRACIÓN</label>
                                                                        <input type="text" class="form-control form-control-sm bg-light" readonly
                                                                               value="{{ $registro->Via_Aplicacion ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">DOSIS SUMINISTRADA</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                               name="dosis_suministrada"
                                                                               value="{{ $interv?->dosis_suministrada ?? $registro->Cantidad ?? '' }}"
                                                                               placeholder="Ej: 500 mg">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">SISTEMA INTERNACIONAL DE UNIDADES</label>
                                                                        <select class="form-control form-control-sm" name="id_sis_internacional">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['sisInternacional'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_sis_internacional ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">FRECUENCIA</label>
                                                                        <select class="form-control form-control-sm" name="id_frecuencia">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['frecuencias'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_frecuencia ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">PERFIL ANTIMICROBIANO</label>
                                                                        <select class="form-control form-control-sm" name="id_perfil_antimicrobiano">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['perfiles'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_perfil_antimicrobiano ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">ESPECIALIDAD TRATANTE</label>
                                                                        <select class="form-control form-control-sm" name="id_esp_tratante">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['espTratantes'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_esp_tratante ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">DIAGNÓSTICO INFECCIOSO</label>
                                                                        <select class="form-control form-control-sm" name="id_diag_infeccioso">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['diagInfecciosos'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_diag_infeccioso ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- SECCIÓN: Adecuación --}}
                                                                <div class="section-title bg-warning text-dark px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-check-circle mr-1"></i> Adecuación del Tratamiento
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">¿DOSIS ADECUADA?</label>
                                                                        <select class="form-control form-control-sm" name="dosis_adecuada">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','No aplica'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->dosis_adecuada ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA FIN ANTIBIÓTICO</label>
                                                                        <input type="date" class="form-control form-control-sm"
                                                                               name="fecha_fin_antibiotico"
                                                                               value="{{ $interv?->fecha_fin_antibiotico?->format('Y-m-d') ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">TIEMPO DE TRATAMIENTO</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                               name="tiempo_tratamiento"
                                                                               value="{{ $interv?->tiempo_tratamiento ?? '' }}"
                                                                               placeholder="Ej: 7 días">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">¿DURACIÓN ADECUADA?</label>
                                                                        <select class="form-control form-control-sm" name="duracion_adecuada">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','No aplica'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->duracion_adecuada ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">INDICACIÓN DE LA TERAPIA</label>
                                                                        <select class="form-control form-control-sm" name="id_indicacion_terapia">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['indicacionesTerapia'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_indicacion_terapia ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">TRATAMIENTO</label>
                                                                        <select class="form-control form-control-sm" name="id_tratamiento">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['tratamientos'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_tratamiento ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- SECCIÓN: Cultivo --}}
                                                                <div class="section-title bg-secondary text-white px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-flask mr-1"></i> Cultivo y Microbiología
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">¿CULTIVO PREVIO?</label>
                                                                        <select class="form-control form-control-sm" name="cultivo_previo">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->cultivo_previo ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA DE MUESTRA</label>
                                                                        <input type="date" class="form-control form-control-sm"
                                                                               name="fecha_muestra"
                                                                               value="{{ $interv?->fecha_muestra?->format('Y-m-d') ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">TIPO DE MUESTRA</label>
                                                                        <select class="form-control form-control-sm" name="id_tipo_muestra">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['tiposMuestra'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_tipo_muestra ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">RESULTADOS</label>
                                                                        <select class="form-control form-control-sm" name="id_resultado">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['resultados'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_resultado ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">MICROORGANISMO</label>
                                                                        <select class="form-control form-control-sm" name="id_microorganismo">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['microorganismos'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_microorganismo ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">PERFIL</label>
                                                                        <select class="form-control form-control-sm" name="id_pantimicrobiano">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($catalogos['pantimicrobianos'] as $item)
                                                                                <option value="{{ $item->id }}" {{ ($interv?->id_pantimicrobiano ?? '') == $item->id ? 'selected' : '' }}>
                                                                                    {{ $item->descripcion }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <label class="proa-label">SOLICITUDES DE PRUEBAS ESPECIALES Y TEST RÁPIDOS</label>
                                                                        <textarea class="form-control form-control-sm" name="solicitudes_pruebas" rows="2"
                                                                                  placeholder="Describa las pruebas especiales solicitadas...">{{ $interv?->solicitudes_pruebas ?? '' }}</textarea>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="proa-label">OPORTUNIDAD DEL REPORTE</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                               name="oportunidad_reporte"
                                                                               value="{{ $interv?->oportunidad_reporte ?? '' }}"
                                                                               placeholder="Ej: 24 horas">
                                                                    </div>
                                                                </div>

                                                                {{-- SECCIÓN: Valoración infectología --}}
                                                                <div class="section-title bg-danger text-white px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-user-md mr-1"></i> Valoración por Infectología
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">VALORACIÓN INFECTOLOGÍA GRUPO 1</label>
                                                                        <select class="form-control form-control-sm" name="valoracion_grupo1">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','No aplica'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->valoracion_grupo1 ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="proa-label">VALORACIÓN INFECTOLOGÍA UCI/UCIN/NEUTROPENIA</label>
                                                                        <select class="form-control form-control-sm" name="valoracion_uci">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','No aplica'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->valoracion_uci ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">FECHA DE LA VALORACIÓN</label>
                                                                        <input type="date" class="form-control form-control-sm"
                                                                               name="fecha_valoracion"
                                                                               value="{{ $interv?->fecha_valoracion?->format('Y-m-d') ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">AJUSTE DE PRESCRIPCIÓN</label>
                                                                        <select class="form-control form-control-sm" name="ajuste_prescripcion">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','No aplica'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->ajuste_prescripcion ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- SECCIÓN: Seguimiento PROA --}}
                                                                <div class="section-title bg-success text-white px-3 py-1 mb-2 rounded">
                                                                    <i class="fas fa-clipboard-check mr-1"></i> Seguimiento PROA
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">ADHERENCIA AL PROA</label>
                                                                        <select class="form-control form-control-sm" name="adherencia_proa">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No','Parcial'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->adherencia_proa ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">¿ADHERENCIA A GUÍAS?</label>
                                                                        <select class="form-control form-control-sm" name="adherencia_guias">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->adherencia_guias ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="proa-label">¿POR QUÉ NO ES ADHERENTE?</label>
                                                                        <input type="text" class="form-control form-control-sm"
                                                                               name="razon_no_adherencia"
                                                                               value="{{ $interv?->razon_no_adherencia ?? '' }}"
                                                                               placeholder="Razón de no adherencia...">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">CASO CERRADO</label>
                                                                        <select class="form-control form-control-sm" name="caso_cerrado">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->caso_cerrado ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label class="proa-label">MORTALIDAD</label>
                                                                        <select class="form-control form-control-sm" name="mortalidad">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach(['Si','No'] as $op)
                                                                                <option value="{{ $op }}" {{ ($interv?->mortalidad ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-12">
                                                                        <label class="proa-label">OBSERVACIÓN</label>
                                                                        <textarea class="form-control form-control-sm" name="observacion" rows="2"
                                                                                  placeholder="Observaciones generales...">{{ $interv?->observacion ?? '' }}</textarea>
                                                                    </div>
                                                                </div>

                                                                </fieldset>

                                                                {{-- Botón guardar --}}
                                                                @unless($bloqueadoProa)
                                                                <div class="d-flex justify-content-end align-items-center">
                                                                    <span class="save-msg text-success mr-3 d-none">
                                                                        <i class="fas fa-check-circle mr-1"></i> Guardado correctamente
                                                                    </span>
                                                                    <button type="button" class="btn btn-success btn-sm btn-guardar-proa">
                                                                        <i class="fas fa-save mr-1"></i> Guardar Intervención
                                                                    </button>
                                                                </div>
                                                                @endunless

                                                                </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- /registro individual --}}

                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        {{-- /medicamento --}}

                                    @endforeach
                                    
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /BLOQUE PROA --}}
                                    @endif

                                </div>
                            </div>
                        </div>
                        {{-- /paciente --}}
                    </div>

                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle mr-2"></i>No se encontraron pacientes en este servicio.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Paginación de pacientes dentro del servicio --}}
                    @if($pacientesPaginados && $pacientesPaginados->hasPages())
                        <div class="d-flex justify-content-center mt-3 mb-2">
                            {{ $pacientesPaginados->links('pagination::bootstrap-4') }}
                        </div>
                    @endif

                </div>
            </div>

        @endif
    </div>

    {{-- Paginación de servicios --}}
    @if($serviciosPaginados->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $serviciosPaginados->links('pagination::bootstrap-4') }}
        </div>
    @endif

@stop

@section('css')
    <style>
        /* Contenedor con scroll para pacientes */
        .pacientes-scroll-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 10px;
        }
        /* Estilos del scrollbar */
        .pacientes-scroll-container::-webkit-scrollbar {
            width: 12px;
        }
        .pacientes-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .pacientes-scroll-container::-webkit-scrollbar-thumb {
            background: #6e9eff;
            border-radius: 10px;
        }
        .pacientes-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #4c7bd9;
        }

        /* Bloques de Epidemiología y PROA — planos (sin gradiente), minimalista */
        .bg-gradient-info {
            background: #2a377e;   /* azul institucional */
        }
        .bg-gradient-success {
            background: #2e7d5b;   /* verde plano para diferenciar PROA */
        }
        .border-info {
            border: 1px solid #e5e7f0 !important;
        }
        .border-success {
            border: 1px solid #e5e7f0 !important;
        }
        .border-purple {
            border: 1px solid #e5e7f0 !important;
        }
        .bg-gradient-purple {
            background: #5a4b8a;
        }
        .btn-purple {
            color: #fff;
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-purple:hover {
            color: #fff;
            background-color: #5a2c91;
            border-color: #54298a;
        }
        .btn-purple:focus, .btn-purple.focus {
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.5);
        }
        .microorganismo-info-form .form-control[readonly] {
            background-color: #ffffff !important;
            border: 1px solid #ced4da;
            cursor: default;
        }

        /* ── Secciones de muestra repetidas dentro de un mismo microorganismo ── */
        .registro-muestra {
            padding: 10px 12px 2px;
            border-radius: 10px;
            transition: background-color 0.18s ease;
        }
        .registro-muestra:hover {
            background: #f7f9fd;
        }
        .registro-muestra-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 8px;
        }
        .registro-muestra-tag {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #2a377e;
            cursor: pointer;
        }
        .registro-muestra-fecha {
            font-size: 0.75rem;
            color: #8b93a5;
        }
        /* Sección seleccionada mediante el checkbox */
        .registro-muestra.is-selected {
            background: #eef1fa;
            box-shadow: inset 3px 0 0 0 #2a377e;
        }
        /* Línea tenue que separa una sección de la siguiente */
        .registro-divider {
            border: 0;
            border-top: 1px dashed #dfe3ee;
            margin: 6px 4px 10px;
        }

        /* Tarjetas de servicio en grid — minimalista, azul institucional */
        .service-card {
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            border-radius: 14px;
            border: 1px solid #e5e7f0 !important;
            overflow: hidden;
        }
        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px -10px rgba(42, 55, 126, 0.45) !important;
        }
        .service-header {
            cursor: pointer;
            background: #2a377e;   /* azul institucional plano */
            color: #fff;
            padding: 16px 18px;
            border-radius: 14px 14px 0 0;
            transition: background-color 0.18s ease;
        }
        .service-header:hover {
            background: #212a63;
            text-decoration: none !important;
        }
        .service-icon {
            font-size: 1.6rem;
            color: #ffffff;
        }
        .service-info h6 {
            font-size: 0.95rem;
            color: #ffffff;
            margin-bottom: 3px;
            font-weight: 700;
        }
        .service-info small {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.8rem;
        }
        .text-light-muted {
            color: rgba(255, 255, 255, 0.82) !important;
        }

        /* Tarjetas de paciente — planas y minimalistas */
        .patient-card {
            margin-bottom: 0 !important;
            border-radius: 14px;
            border: 1px solid #e5e7f0;
        }
        .patient-card > .card-header {
            border-left: 4px solid #2a377e;
            padding: 12px 16px !important;
            background: #eef1fa;
        }
        .patient-card > .card-header strong {
            font-size: 1rem;
        }
        .patient-card > .card-header small {
            font-size: 0.85rem;
        }
        .patient-avatar {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dde3f5;
            color: #2a377e;
            border-radius: 50%;
        }
        .patient-avatar i {
            font-size: 1.1rem;
            color: #2a377e !important;
        }
        .patient-header {
            cursor: pointer;
            transition: background-color 0.18s ease;
        }
        .patient-header:hover {
            background: #e4e9f7 !important;
        }

        /* Tarjetas de medicamento — planas */
        .med-card {
            margin-bottom: 8px;
            border-radius: 10px;
            border: 1px solid #e5e7f0;
        }
        .med-card > .card-header {
            border-left: 3px solid #2e7d5b;
            padding: 7px 10px !important;
            font-size: 0.85rem;
            background: #f3f7f4;
        }
        .med-header {
            cursor: pointer;
            transition: background-color 0.18s ease;
        }
        .med-header:hover {
            background: #e8f0ea !important;
        }

        /* Tarjetas de registro */
        .reg-card {
            margin-bottom: 6px;
            border: 1px solid #e3e6f0;
        }
        .reg-card > .card-header {
            border-left: 3px solid #6c757d;
            padding: 6px 10px !important;
            font-size: 0.8rem;
            background-color: #fafbfc;
        }
        .reg-header {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .reg-header:hover {
            background-color: #f1f3f5 !important;
        }

        /* Iconos de colapso */
        .collapse-icon {
            transition: transform 0.25s ease;
            font-size: 0.85rem;
        }
        [aria-expanded="true"] .collapse-icon {
            transform: rotate(180deg);
        }

        /* Badges */
        .badge-sm {
            font-size: 0.7rem;
            padding: 3px 7px;
        }

        /* Formulario PROA */
        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            padding: 8px 16px !important;
        }
        .proa-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #495057;
            display: block;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .proa-form .form-control-sm {
            font-size: 0.85rem;
            height: calc(1.6em + 0.6rem + 2px);
            padding: 0.4rem 0.6rem;
        }
        .proa-form select.form-control-sm {
            font-size: 0.85rem;
            height: calc(1.6em + 0.6rem + 2px);
        }
        .proa-form textarea.form-control-sm {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
        }
        .proa-form .row {
            margin-bottom: 0;
        }
        .proa-form .row.mb-3 {
            margin-bottom: 1rem !important;
        }
        .proa-form .col-md-1,
        .proa-form .col-md-2,
        .proa-form .col-md-3,
        .proa-form .col-md-4,
        .proa-form .col-md-6,
        .proa-form .col-md-12 {
            padding-left: 6px;
            padding-right: 6px;
            margin-bottom: 8px;
        }

        /* Utilidades */
        .text-sm {
            font-size: 0.82rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            // Rotar ícono de flecha al abrir/cerrar
            $('.collapse').on('show.bs.collapse', function () {
                var header = $('[data-target="#' + $(this).attr('id') + '"]');
                header.attr('aria-expanded', 'true');
            }).on('hide.bs.collapse', function () {
                var header = $('[data-target="#' + $(this).attr('id') + '"]');
                header.attr('aria-expanded', 'false');
            });

            // Guardar formulario "Información adicional de (microorganismo)" via AJAX
            $(document).on('click', '.btn-registrar-info', function () {
                var $btn = $(this);
                var $form = $btn.closest('.microorganismo-info-form');
                var $msg  = $form.find('.info-save-msg');
                var token = $('meta[name="csrf-token"]').attr('content') || $form.find('[name="_token"]').val();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
                $msg.addClass('d-none');

                $.ajax({
                    url: '{{ route("epidemiologia.guardar") }}',
                    method: 'POST',
                    data: $form.serialize(),
                    headers: { 'X-CSRF-TOKEN': token },
                    success: function (resp) {
                        if (resp.success) {
                            $msg.removeClass('d-none');
                            setTimeout(function () { $msg.addClass('d-none'); }, 4000);
                        } else {
                            alert('Error al registrar: ' + (resp.message || 'Error desconocido'));
                        }
                    },
                    error: function (xhr) {
                        var errMsg = 'Error al registrar la información.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg += '\n' + xhr.responseJSON.message;
                        }
                        alert(errMsg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Registrar');
                    }
                });
            });

            // Guardar formulario PROA via AJAX
            $(document).on('click', '.btn-guardar-proa', function () {
                var $btn = $(this);
                var $form = $btn.closest('.proa-form');
                var $msg  = $form.find('.save-msg');

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');
                $msg.addClass('d-none');

                var formData = $form.serializeArray();
                // Incluir el token CSRF manualmente si serializeArray lo omite
                var token = $('meta[name="csrf-token"]').attr('content');
                if (!token) {
                    formData.push({ name: '_token', value: $form.find('[name="_token"]').val() });
                }

                $.ajax({
                    url: '{{ route("intervenciones-proa.guardar") }}',
                    method: 'POST',
                    data: formData,
                    headers: { 'X-CSRF-TOKEN': token || $form.find('[name="_token"]').val() },
                    success: function (resp) {
                        if (resp.success) {
                            $msg.removeClass('d-none');
                            setTimeout(function () { $msg.addClass('d-none'); }, 4000);
                        } else {
                            alert('Error al guardar: ' + (resp.message || 'Error desconocido'));
                        }
                    },
                    error: function (xhr) {
                        var errMsg = 'Error al guardar la intervención.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg += '\n' + xhr.responseJSON.message;
                        }
                        alert(errMsg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Intervención');
                    }
                });
            });

            // Campos constantes del paciente: al cambiarlos en un microorganismo,
            // replicar el valor en los demás microorganismos del MISMO paciente.
            $(document).on('input change', '.microorganismo-info-form .const-field', function () {
                var $field = $(this);
                var name   = $field.attr('name');
                var val    = $field.val();
                var $scope = $field.closest('.patient-card');
                var origin = $field.get(0);

                $scope.find('.microorganismo-info-form [name="' + name + '"]').each(function () {
                    if (this !== origin) {
                        $(this).val(val);
                    }
                });
            });

            // ============================================================
            // PROCEDENCIA: Departamento y Municipio desde la API del DANE
            // (Divipola - datos.gov.co). Los municipios se filtran por cod_dpto.
            // ============================================================
            var DANE_URL = 'https://www.datos.gov.co/resource/gdxc-w37w.json';
            var deptosPromise = null;
            var mpiosCache = {};

            function daneFetch(url, cacheKey) {
                var guardado = null;
                try { guardado = sessionStorage.getItem(cacheKey); } catch (e) {}
                if (guardado) {
                    return Promise.resolve(JSON.parse(guardado));
                }
                return fetch(url)
                    .then(function (r) {
                        if (!r.ok) { throw new Error('HTTP ' + r.status); }
                        return r.json();
                    })
                    .then(function (data) {
                        try { sessionStorage.setItem(cacheKey, JSON.stringify(data)); } catch (e) {}
                        return data;
                    });
            }

            function getDeptos() {
                if (!deptosPromise) {
                    var url = DANE_URL + '?$select=cod_dpto,dpto&$group=cod_dpto,dpto&$order=dpto&$limit=100';
                    deptosPromise = daneFetch(url, 'dane_deptos');
                }
                return deptosPromise;
            }

            function getMpios(cod) {
                if (!mpiosCache[cod]) {
                    var url = DANE_URL + "?$select=cod_mpio,nom_mpio&$where=cod_dpto='" + cod + "'&$order=nom_mpio&$limit=1000";
                    mpiosCache[cod] = daneFetch(url, 'dane_mpios_' + cod);
                }
                return mpiosCache[cod];
            }

            function llenarDeptos($sel, deptos) {
                var actual = $sel.attr('data-selected') || $sel.val() || '';
                $sel.empty().append($('<option>').val('').text('— Seleccionar —'));
                deptos.forEach(function (d) {
                    var $o = $('<option>').val(d.dpto).text(d.dpto).attr('data-cod', d.cod_dpto);
                    if (d.dpto === actual) { $o.prop('selected', true); }
                    $sel.append($o);
                });
            }

            function llenarMpios($sel, mpios, seleccionar) {
                $sel.empty().append($('<option>').val('').text('— Seleccionar —'));
                mpios.forEach(function (m) {
                    var $o = $('<option>').val(m.nom_mpio).text(m.nom_mpio);
                    if (m.nom_mpio === seleccionar) { $o.prop('selected', true); }
                    $sel.append($o);
                });
            }

            function mpioDe($depto) {
                return $depto.closest('.procedencia-box').find('select.dane-mpio');
            }

            function codSeleccionado($depto) {
                return $depto.find('option:selected').attr('data-cod') || '';
            }

            // Carga inicial: llenar departamentos y, si hay valor guardado, sus municipios.
            getDeptos().then(function (deptos) {
                $('select.dane-depto').each(function () {
                    var $d = $(this);
                    llenarDeptos($d, deptos);

                    var cod = codSeleccionado($d);
                    if (!cod) { return; }
                    var $m = mpioDe($d);
                    var guardado = $m.attr('data-selected') || '';
                    getMpios(cod).then(function (mpios) {
                        llenarMpios($m, mpios, guardado);
                    }).catch(function (e) { console.warn('DANE municipios:', e); });
                });
            }).catch(function (e) {
                console.warn('No se pudo cargar la lista de departamentos del DANE:', e);
            });

            // Al cambiar el departamento: replicar en todos los bloques del paciente
            // y recargar la lista de municipios correspondiente.
            $(document).on('change', 'select.dane-depto', function () {
                var $src = $(this);
                var valor = $src.val();
                var cod = codSeleccionado($src);
                var $scope = $src.closest('.patient-card');

                $scope.find('select.dane-depto').val(valor);

                var $mpios = $scope.find('select.dane-mpio');
                if (!cod) {
                    $mpios.empty().append($('<option>').val('').text('— Seleccionar —'));
                    return;
                }
                getMpios(cod).then(function (mpios) {
                    $mpios.each(function () { llenarMpios($(this), mpios, ''); });
                }).catch(function (e) { console.warn('DANE municipios:', e); });
            });

            // Al cambiar el municipio: replicarlo en los demás bloques del paciente.
            $(document).on('change', 'select.dane-mpio', function () {
                var $src = $(this);
                $src.closest('.patient-card').find('select.dane-mpio').val($src.val());
            });

            // Recalcular clasificación en texto y días entre qx e infección en cambio de campos
            $(document).on('change', '.microorganismo-info-form select[name="sitio"], .microorganismo-info-form select[name="tipo"], .microorganismo-info-form select[name="clasificacion"]', function () {
                var $form = $(this).closest('.microorganismo-info-form');
                var sitio = $form.find('[name="sitio"]').val();
                var tipo = $form.find('[name="tipo"]').val();
                var clasificacion = $form.find('[name="clasificacion"]').val();
                var $clasificacionTexto = $form.find('[name="clasificacion_texto"]');

                if (!tipo || !clasificacion) {
                    $clasificacionTexto.val('');
                    return;
                }

                var texto = 'No aplica';

                if (tipo == '1') {
                    if (clasificacion == '1') {
                        texto = 'Colonización Intrahospitalaria';
                    } else if (clasificacion == '2') {
                        texto = 'Contaminado Intrahospitalaria';
                    } else if (clasificacion == '3') {
                        texto = 'Infección';
                    } else if (clasificacion == '4') {
                        if (sitio === '51. Infeccion Previa') {
                            texto = 'Infección Previa';
                        } else if (sitio === '52. No cumple criterios') {
                            texto = 'No Cumple Criterios';
                        } else {
                            texto = 'No aplica';
                        }
                    } else if (clasificacion == '5') {
                        texto = 'Infección sin Mios';
                    } else if (clasificacion == '6') {
                        texto = 'Infección Polimicrobiano';
                    } else if (clasificacion == '7') {
                        texto = 'Complicación';
                    }
                } else if (tipo == '2') {
                    if (clasificacion == '1') {
                        texto = 'Colonización Extrahospitalaria';
                    } else if (clasificacion == '2') {
                        texto = 'Contaminado Extrahospitalaria';
                    } else if (clasificacion == '3') {
                        texto = 'Infección Extrahospitalaria';
                    }
                }

                $clasificacionTexto.val(texto);
            });

            // Resaltar la sección de campos cuyo checkbox está marcado
            $(document).on('change', '.registro-check', function () {
                $(this).closest('.registro-muestra').toggleClass('is-selected', this.checked);
            });

            $(document).on('change', '.microorganismo-info-form [name="fecha_quirurgica_previa"], .microorganismo-info-form .fecha-muestra', function () {
                var $form = $(this).closest('.microorganismo-info-form');

                var fechaQxVal = $form.find('[name="fecha_quirurgica_previa"]').val();
                // La fecha de referencia es la del primer registro del bloque.
                var fechaMuestraVal = $form.find('.fecha-muestra').first().val();
                var $diasInput = $form.find('[name="dias_entre_qx_e_infeccion"]');

                if (!fechaQxVal || !fechaMuestraVal) {
                    $diasInput.val('');
                    return;
                }

                var qxDate = new Date(fechaQxVal);
                var muestraDate = new Date(fechaMuestraVal);

                // Fórmula: (fecha toma de muestra) - (fecha quirúrgica previa), en días
                var diffTime = muestraDate - qxDate;
                var diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

                $diasInput.val(isNaN(diffDays) ? '' : diffDays);
            });

            // Disparar cálculos para registros que ya tienen valores
            setTimeout(function() {
                $('.microorganismo-info-form').each(function () {
                    var $form = $(this);
                    $form.find('select[name="tipo"]').trigger('change');
                    $form.find('[name="fecha_quirurgica_previa"]').trigger('change');
                });
            }, 500);
        });
    </script>
@stop
