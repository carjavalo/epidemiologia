@extends('adminlte::page')

@section('title', 'Registros por Servicio')

@section('content_top_nav_right')
    @include('partials.notificaciones-bell')
@stop

@section('content_header')
    {{-- El conteo vive ahora en la barra de contexto, donde además es correcto:
         aquí mostraba "1 servicio(s)" al entrar en uno, que confundía. --}}
    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: -10px;">
        <h1 style="font-size: 1.6rem; margin-bottom: 0;"><i class="fas fa-hospital mr-2"></i>Registros por Servicio</h1>
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

        // ¿El usuario ve los campos exclusivos de enfermería? (req. 7, 8, 11).
        // Enfermero sí; auxiliar no. Los administradores también (supervisión).
        $esEnfermero = auth()->check() && (auth()->user()->esEnfermero() || auth()->user()->esAdmin());

        // Listado oficial del campo SITIO (req. 2.1). Transcrito del documento de
        // requerimientos; la fuente de verdad es la columna SITIO de Camilo.xlsx.
        $opcionesSitio = [
            '1. Absceso Espinal sin Meningitis',
            '2. Absceso Mamariomastitis',
            '3. Conjuntivitis',
            '4. Cicuncision en Recien Nacidos',
            '5. Endocarditis',
            '6. Endometritis',
            '7. Enterocolitis Necrotizante',
            '8. Gastroenteritis',
            '9. Herida Quirúrgica Incisional Superficial',
            '10. Herida Quirúrgica Incisional Superficial Secundaria',
            '11. Herida Quirúrgica Incisional Profunda',
            '12. Herida Quirurgica Organo/Espacio',
            '13. Infeccion de Cavidad (Boca, Lengua, Encias)',
            '14. Infección de la Articulacion o Bursa',
            '15. Infeccion de la Cúpula Vaginal',
            '15. Infección de la Episiotomia',
            '16. Infección de Tracto Respiratorio Superior',
            '17. Infección del Espacio Intervertebral',
            '18. Infección del Oido/mastoides',
            '19. Infeccion del Torrente Sanguineo - Cateter Mahurkar',
            '19. Infeccion del Torrente Sanguineo - Cateter implantable',
            '19. Infeccion del Torrente Sanguineo - Cateter umbilical',
            '19. Infeccion del Torrente Sanguineo Linea Vascular - CVC',
            '19. Infeccion del otros Torrente Sanguineo Linea Vascular - PICC',
            '20. Infeccion del Torrente Sanguineo Linea Vascular con otro foco',
            '21. Infeccion del Tracto Gastrointestinal',
            '22. Infeccion Intraabdominal',
            '23. Infeccion Intracraneal',
            '24. Infeccion Sintomática del Tracto Urinario',
            '25. Infeccion Sintomática del Tracto Urinario con Sonda',
            '26. Infeccion Sistémica/Disemiada',
            '27. Infección Venosa o Arterial',
            '27. Infección arte',
            '28. Mediastinitis',
            '29. Meningitis o Ventriculitis',
            '30. Miocarditis o Pericarditis',
            '31. Neumonia 1 - 2 - 3',
            '32. Neumonia Asociada Intubación 1 - 2 - 3',
            '33. Neumonia Asociada a Ventilador 1 - 2 - 3',
            '34. Ojo Exepto Conjuntivitis',
            '35. Onfalitis',
            '36. Osteomielitis',
            '37. Otras Infecciones del Tracto Reproductivo',
            '38. Otras Infecciones del Tracto Respiratorio Inferior',
            '39. Otras Infecciones del Tracto Urinario',
            '40. Otros',
            '41. Piel',
            '41. Piel - Flebitis',
            '42. Pustulosis Infantil',
            '43. Quemadura',
            '44. Sepsis Clinica',
            '45. Sinusitis',
            '46. Tejidos Blandos',
            '47. Traquea-Bronquios-Bronquiolos (Sin neumonia)',
            '48. Ulcera por Decúbito',
            '49. Bacteriuria Asintomatica Bacteremica',
            '50. No aplica',
            '51. Infeccion Previa',
            '52. No cumple criterios',
            '53. LA - Infeccion del Torrente Sanguineo sin Linea Vascular - Arterial',
            '54. Herida Quirúrgica Incisional Profunda Primaria',
            '55. Herida Quirúrgica Incisional Profunda Secundaria',
            '56. Herida Quirúrgica Incisional Superficial Primaria',
            '57. Covid-19',
        ];

        // Opciones oficiales tomadas de Camilo.xlsx.
        $opcionesEspecialidadQx = [
            'CIRUGIA CARDIOVASCULAR', 'CIRUGIA DE CABEZA Y CUELLO', 'CIRUGIA DE MAMA', 'CIRUGIA DE TORAX',
            'CIRUGIA GASTRO ONCOLOGICA', 'CIRUGIA GASTROINTESTINAL-HEPATOBILIAR', 'CIRUGIA GENERAL',
            'CIRUGIA LAPAROSCOPIA', 'CIRUGIA MAXILOFACIAL', 'CIRUGIA ONCOLOGICA', 'CIRUGIA PEDIATRICA',
            'CIRUGIA PLASTICA', 'CIRUGIA QUEMADOS', 'CIRUGIA TRASPLANTE Y ORGANO ABDOMINAL',
            'CIRUGIA TRAUMA Y EMERGENCIA', 'GASTROENTEROLOGÍA', 'GINECOLOGIA ONCOLOGICA',
            'GINECOLOGIA Y OBSTETRICIA', 'NEUROCIRUGIA', 'NEUROTOLOGIA', 'ODONTOLOGIA GRAL.',
            'OFTALMOLOGIA', 'ORTOPEDIA', 'OTORRINOLARINGOLOGIA', 'RADIOLOGIA INTERVENCIONISTA', 'UROLOGIA',
        ];
        $opcionesAntibioticos = [
            'Amikacina', 'Amoxicilina/ A.Clavulamico', 'Ampicilina', 'Ampicilina/ Sulbactam', 'Anfotericina',
            'Anidulofungina', 'Aztreonam', 'Caspofungina', 'Cefazolina', 'Cefepime', 'Cefoperazona / Sulbactam',
            'Cefotaxime', 'Cefoxitin', 'Ceftazidima', 'Ceftriaxone', 'Cefuroxime', 'Ciprofloxacina',
            'Claritromicina', 'Clindamicina', 'Colistina', 'Daptomicina', 'Ertapenem', 'Fluconazol',
            'Gentamicina', 'Imipenem', 'Linezolid', 'Meropenem', 'Metronidazol', 'Oxacilina', 'Penicilina',
            'Piperacilina / Tazobactam', 'Polimixina', 'Rifampicina', 'Tigeciclina',
            'Trimetoprim / Sulfametoxazol', 'Vancomicina', 'NO APLICA',
        ];
        $opcionesAsa = ['1', '2', '3', '4', '5', '6', 'Sin Registro'];

        // TIPO DE MUESTRA — 33 opciones oficiales (Camilo.xlsx).
        $opcionesTipoMuestra = [
            '1. Absceso tejidos organos', '2. Cepillado o LBA', '3. Coleccion piel y tejidps blandos',
            '4. Hemocultivo barrido cateter', '5. Liquido cefalorraquideo', '6. Liquido peritoneal',
            '7. Liquido pleural', '8. Materia fecal', '9. Orina', '10. Orina por cateterismo vesical',
            '11. Orina por miccion expontanea', '12. Orina por sonda vesical', '13. Otro tipo de muestra',
            '14. Punta de cateter (VP)', '15. Punta de cateter (CVC - PICC)', '16. Sangre - puncion periferica',
            '17. Secrecion aspirado traqueal', '18. Secrecion de piel y tejidos blandos',
            '19. Secrecion herida quirurgica', '20. Secrecion sitio CVC', '21. Segmento tejido',
            '22. Liquido sinovial', '23. Liquido pericardio', '24. Esputo', '25. Secrecion ocular',
            '26. Hisopado rectal', '27. Hisopado nasal', '28. Hisopado rectovaginal',
            '29. Hemocultivo puncion arterial', '30. Aspirado medula osea', '31. Biopsia', '32. Otros',
            '99. No Aplica',
        ];

        // TIPO DE DOCUMENTO — opciones de Camilo.xlsx.
        $opcionesTipoDocumento = [
            'CC', 'TI', 'RC', 'RN', 'CE', 'PT', 'PA', 'SC', 'AS', 'CN', 'URG',
            'ARG', 'BOL', 'BRA', 'CHI', 'ECU', 'PAR', 'PER', 'VEN',
        ];
    @endphp

    {{-- Contenedor que se intercambia por AJAX en búsquedas/filtros/navegación
         (sin recargar). Incluye el botón volver, el buscador y los resultados. --}}
    <div id="registros-resultados">

    @php
        $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                  7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

        // ── Datos de la barra de contexto ───────────────────────────────────
        // El nombre del servicio abierto no se imprimía en ninguna parte: la
        // única pista de dónde estabas era el texto del botón "Volver".
        $ctxServicio  = $servicioSeleccionado
            ? (optional($serviciosPaginados->first())->ubicacion ?: $servicioSeleccionado)
            : null;
        $ctxPacientes = $ctxServicio
            ? ($pacientesPaginados ? $pacientesPaginados->total() : ($pacientesCountPorServicio[$ctxServicio] ?? 0))
            : 0;
        $ctxProa      = $ctxServicio ? ($proaCountPorServicio[$ctxServicio] ?? 0) : 0;
        $ctxServicios = method_exists($serviciosPaginados, 'total')
            ? $serviciosPaginados->total()
            : count($serviciosPaginados);

        // ── Fichas de filtro activo ─────────────────────────────────────────
        // Cada una lleva su propio enlace para quitarse sin tocar las demás.
        $paramsBase = array_filter([
            'servicio' => $servicioSeleccionado,
            'search'   => $search,
            'anio'     => $anio,
            'mes'      => $mes,
            'tipo'     => ($tipo ?? 'todos') !== 'todos' ? $tipo : null,
        ]);
        $sinFiltro = fn (array $claves) => route('registros.index', array_diff_key($paramsBase, array_flip($claves)));

        $filtrosActivos = [];
        if ($search) {
            $filtrosActivos[] = ['texto' => 'Búsqueda: ' . $search, 'url' => $sinFiltro(['search'])];
        }
        if ($anio || $mes) {
            $txtFecha = trim(($mes ? ($meses[(int) $mes] ?? '') . ' ' : '') . ($anio ?: ''));
            $filtrosActivos[] = ['texto' => $txtFecha !== '' ? $txtFecha : 'Fecha', 'url' => $sinFiltro(['anio', 'mes'])];
        }
        if (($tipo ?? 'todos') !== 'todos') {
            $etiquetasTipo = ['proa' => 'Solo con PROA', 'epidemiologia' => 'Solo epidemiología'];
            $filtrosActivos[] = ['texto' => $etiquetasTipo[$tipo] ?? $tipo, 'url' => $sinFiltro(['tipo'])];
        }
    @endphp

    {{-- Barra de contexto: dice en qué servicio estás y, al desplegar un
         paciente, también en cuál. Se ancla arriba con position:sticky. --}}
    <div class="r-ctx">
        @if($servicioSeleccionado)
            <a href="{{ route('registros.index') }}" class="r-ctx-volver js-nav" title="Volver a servicios">
                <i class="fas fa-arrow-left"></i>
            </a>
        @endif
        <div class="r-ctx-tit">
            <span class="r-ctx-ruta">
                @if($servicioSeleccionado)
                    <a href="{{ route('registros.index') }}" class="js-nav">Servicios</a>
                    <i class="fas fa-chevron-right" style="font-size:.58rem"></i>
                @else
                    Registros por servicio
                @endif
            </span>
            <h3>{{ $ctxServicio ?? 'Servicios' }}</h3>
        </div>

        {{-- Paciente abierto. Lo rellena el JS al desplegar su tarjeta, para no
             necesitar una segunda cabecera fija encima de la primera. --}}
        <span class="r-ctx-paciente js-ctx-paciente">
            <i class="fas fa-user-injured"></i> <b></b>
        </span>

        <div class="r-ctx-nums">
            @if($servicioSeleccionado)
                <span class="r-chip r-chip--neut">
                    <i class="fas fa-users mr-1"></i><b>{{ $ctxPacientes }}</b>&nbsp;{{ $ctxPacientes == 1 ? 'paciente' : 'pacientes' }}
                </span>
                @if($ctxProa > 0)
                    <span class="r-chip r-chip--ok" title="Pacientes con intervención PROA registrada">
                        <i class="fas fa-capsules mr-1"></i><b>{{ $ctxProa }}</b>&nbsp;con PROA
                    </span>
                @endif
            @else
                <span class="r-chip r-chip--neut">
                    <i class="fas fa-hospital-alt mr-1"></i><b>{{ $ctxServicios }}</b>&nbsp;{{ $ctxServicios == 1 ? 'servicio' : 'servicios' }}
                </span>
            @endif
        </div>
    </div>

    {{-- Buscador y filtros en una sola línea. El botón "Filtrar" desapareció:
         los selects ya recargan solos con onchange, así que no hacía nada. --}}
    <form method="GET" action="{{ route('registros.index') }}" class="r-buscar">
        @if($servicioSeleccionado)
            <input type="hidden" name="servicio" value="{{ $servicioSeleccionado }}">
        @endif
        <label class="r-buscar-caja">
            <i class="fas fa-search"></i>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Buscar por servicio, paciente o documento…" autofocus>
        </label>
        <select name="anio" class="r-select" onchange="$(this.form).trigger('submit')" title="Año">
            <option value="">Año: todos</option>
            @foreach($aniosDisponibles as $a)
                <option value="{{ $a }}" {{ (string) $anio === (string) $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <select name="mes" class="r-select" onchange="$(this.form).trigger('submit')" title="Mes">
            <option value="">Mes: todos</option>
            @foreach($meses as $num => $nombre)
                <option value="{{ $num }}" {{ (string) $mes === (string) $num ? 'selected' : '' }}>{{ $nombre }}</option>
            @endforeach
        </select>
        <select name="tipo" class="r-select" onchange="$(this.form).trigger('submit')" title="Tipo de registro">
            <option value="todos" {{ ($tipo ?? 'todos') === 'todos' ? 'selected' : '' }}>Todos los pacientes</option>
            <option value="proa" {{ ($tipo ?? '') === 'proa' ? 'selected' : '' }}>Solo con PROA</option>
            <option value="epidemiologia" {{ ($tipo ?? '') === 'epidemiologia' ? 'selected' : '' }}>Solo epidemiología</option>
        </select>
        <button type="submit" class="r-btn">Buscar</button>
    </form>

    @if(count($filtrosActivos))
        <div class="r-filtros">
            <span class="r-filtros-rot">Filtros</span>
            @foreach($filtrosActivos as $f)
                <span class="r-filtro">
                    {{ $f['texto'] }}
                    <a href="{{ $f['url'] }}" class="js-nav" title="Quitar este filtro"><i class="fas fa-times"></i></a>
                </span>
            @endforeach
            <a href="{{ route('registros.index', array_filter(['servicio' => $servicioSeleccionado])) }}"
               class="js-nav r-btn-tenue" style="color:#6b7280;">Limpiar todo</a>
        </div>
    @endif

    {{-- GRID PRINCIPAL --}}
    <div class="row {{ $servicioSeleccionado ? 'justify-content-center' : '' }}">
        @if(!$servicioSeleccionado)
            {{-- ============================================ --}}
            {{-- VISTA SERVICIOS: Grid de tarjetas de servicios --}}
            {{-- ============================================ --}}
            @forelse($serviciosPaginados as $servicio)
                @php
                    $servicioNombre = $servicio->ubicacion;
                    // Conteo directo de pacientes por servicio (correcto y eficiente).
                    $totalPacientes = $pacientesCountPorServicio[$servicioNombre] ?? 0;
                    $totalProaServicio = $proaCountPorServicio[$servicioNombre] ?? 0;
                @endphp

                <div class="col-servicio mb-3">
                    <a href="{{ route('registros.index', ['servicio' => $servicioNombre]) }}"
                       class="service-tile" title="{{ $servicioNombre }}">
                        <div class="service-tile-top">
                            <span class="service-tile-ico"><i class="fas fa-hospital-alt"></i></span>
                            <i class="fas fa-chevron-right service-tile-arrow"></i>
                        </div>
                        <h6 class="service-tile-name">{{ Str::limit($servicioNombre, 42) }}</h6>
                        <div class="service-tile-stats">
                            <span class="service-tile-pac">
                                <i class="fas fa-users mr-1"></i>{{ $totalPacientes }} paciente(s)
                            </span>
                            @if($totalProaServicio > 0)
                                <span class="service-tile-proa" title="Pacientes con intervención PROA registrada">
                                    <i class="fas fa-capsules mr-1"></i>{{ $totalProaServicio }} PROA
                                </span>
                            @endif
                        </div>
                    </a>
                </div>

            @empty
                <div class="col-12">
                    @if(!empty($avisoSoloProa))
                        <div class="alert alert-warning">
                            <h6 class="mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Paciente sin microbiología</h6>
                            <p class="mb-1">
                                El paciente <strong>{{ $avisoSoloProa['nombre'] ?: ('documento ' . $avisoSoloProa['documento']) }}</strong>
                                (doc. {{ $avisoSoloProa['documento'] }}@if(!empty($avisoSoloProa['sala'])) · {{ $avisoSoloProa['sala'] }}@endif)
                                tiene <strong>tratamiento PROA</strong> pero <strong>no tiene registros de microbiología / epidemiología</strong>.
                            </p>
                            <small class="text-muted">Por eso no aparece en la vista por servicios. Su información de tratamiento está en la base de PROA.</small>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle mr-2"></i>No se encontraron servicios.
                        </div>
                    @endif
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
                    </div>
                </div>
            @endif

            {{-- Catálogo CIE-10 compartido para el buscador de diagnóstico (una sola vez) --}}
            <datalist id="lista-diagnosticos">
                @foreach(($catalogos['diagnosticos'] ?? collect()) as $dx)
                    <option value="{{ $dx->codigo }} - {{ $dx->descripcion }}"></option>
                @endforeach
            </datalist>

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
                                    // Estado general del paciente: completo si epidemiología está toda
                                    // registrada y (no tiene PROA o PROA también está completo).
                                    $pacienteCompleto     = $info['epi_completo'] && (!$tienePROA || $info['proa_completo']);

                                    // ── Resumen del paciente (qué falta, sin abrir el bloque) ───────
                                    // El agrupamiento por caso y el estado de cada medicamento se calculan
                                    // aquí una sola vez y se reutilizan más abajo, en los bloques de
                                    // epidemiología y PROA. Antes se hacían dentro de cada bloque, o sea
                                    // después de imprimir esta cabecera, que es donde hacen falta.
                                    $gruposMicro = collect($info['seguimientos'])->groupBy(function ($r) {
                                        if (!empty($r->caso_id)) {
                                            return 'caso-' . $r->caso_id;
                                        }
                                        $n = trim((string) ($r->microorganismo ?? ''));
                                        return $n === '' ? '__SIN_MICROORGANISMO__' : mb_strtoupper($n, 'UTF-8');
                                    });
                                    $totalMicro        = $gruposMicro->count();
                                    $microSinRegistrar = $gruposMicro->filter(
                                        fn ($g) => ! collect($g)->every(fn ($r) => (bool) $r->registrado)
                                    )->count();

                                    // Estado de cada medicamento PROA: sus cursos de 7 días y si ya tiene
                                    // intervención registrada en todos ellos.
                                    $estadoMedicamentos = collect();
                                    if ($tienePROA) {
                                        $estadoMedicamentos = $medicamentosPorPaciente->map(function ($regs) use ($intervenciones) {
                                            $cursos = \App\Support\ProaCursos::agrupar($regs);
                                            return [
                                                'cursos'     => $cursos,
                                                'total'      => count($cursos),
                                                'registrado' => collect($cursos)->every(
                                                    fn ($c) => $c['representativa'] && $intervenciones->has($c['representativa']->id)
                                                ),
                                            ];
                                        });
                                    }
                                    $medSinRegistrar = $estadoMedicamentos->filter(fn ($m) => ! $m['registrado'])->count();

                                    // Texto del chip de la cabecera.
                                    $totalPendientes = $microSinRegistrar + $medSinRegistrar;
                                    $textoEstado     = $pacienteCompleto
                                        ? 'Completo'
                                        : ($totalPendientes > 0
                                            ? $totalPendientes . ' ' . ($totalPendientes == 1 ? 'pendiente' : 'pendientes')
                                            : 'Falta registrar');
                                @endphp

                                <div class="col-xl-9 col-lg-10 col-md-12 mb-3">
                                    <div class="card patient-card shadow-lg">
                            <div class="card-header patient-header js-patient-block {{ $pacienteCompleto ? 'r-rail-ok' : 'r-rail-pend' }}" data-toggle="collapse" data-target="#{{ $pacienteKey }}" role="button">
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
                                        {{-- Chip de estado del paciente. Lo actualiza en vivo
                                             actualizarEstadoPaciente() al guardar por AJAX. --}}
                                        <span class="r-chip js-chip-estado js-chip-paciente mr-2 {{ $pacienteCompleto ? 'r-chip--ok' : 'r-chip--pend' }}"
                                              title="{{ $pacienteCompleto ? 'Paciente completo' : 'Quedan registros por completar' }}">
                                            <i class="fas {{ $pacienteCompleto ? 'fa-check' : 'fa-exclamation-circle' }} mr-1"></i>{{ $textoEstado }}
                                        </span>
                                        @if($tienePROA)
                                            <span class="r-chip r-chip--neut mr-2" title="Medicamentos con seguimiento PROA">
                                                <b>{{ $info['total_medic'] }}</b>&nbsp;medicamentos
                                            </span>
                                        @else
                                            <span class="r-chip r-chip--info mr-2" title="Paciente solo en epidemiología">
                                                <i class="fas fa-vial mr-1"></i>Solo epidemiología
                                            </span>
                                        @endif
                                        <i class="fas fa-chevron-down collapse-icon text-muted" style="font-size: 1rem;"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Resumen de lo que falta. Siempre visible: evita abrir el paciente
                                 solo para descubrir que ya estaba todo registrado. --}}
                            <div class="r-resumen">
                                <span class="r-resumen-item js-resumen-epi r-resumen-item--{{ $info['epi_completo'] ? 'ok' : 'pend' }}">
                                    <i class="fas {{ $info['epi_completo'] ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                                    Epidemiología
                                </span>
                                <span class="r-resumen-sep"></span>
                                <span class="r-resumen-item">
                                    <i class="fas fa-vial"></i>
                                    <b>{{ $totalMicro }}</b>&nbsp;{{ $totalMicro == 1 ? 'microorganismo' : 'microorganismos' }}<!--
                                    --><span class="js-resumen-micro-falta"{!! $microSinRegistrar ? '' : ' style="display:none"' !!}>{{ $microSinRegistrar ? ' · ' . $microSinRegistrar . ' sin registrar' : '' }}</span>
                                </span>
                                <span class="r-resumen-sep"></span>
                                @if($tienePROA)
                                    <span class="r-resumen-item">
                                        <i class="fas fa-capsules"></i>
                                        PROA <b>{{ $info['total_medic'] }}</b>&nbsp;{{ $info['total_medic'] == 1 ? 'medicamento' : 'medicamentos' }}<!--
                                        --><span class="js-resumen-proa-falta"{!! $medSinRegistrar ? '' : ' style="display:none"' !!}>{{ $medSinRegistrar ? ' · ' . $medSinRegistrar . ' sin intervención' : '' }}</span>
                                    </span>
                                @else
                                    <span class="r-resumen-item"><i class="fas fa-vial"></i> Sin PROA</span>
                                @endif
                            </div>


                            {{-- Contenido del paciente: Epidemiología y PROA --}}
                            <div id="{{ $pacienteKey }}" class="collapse">
                                <div class="card-body p-3">

                                    {{-- ========================================== --}}
                                    {{-- BLOQUE 1: EPIDEMIOLOGÍA --}}
                                    {{-- ========================================== --}}
                                    @php $epiKey = $pacienteKey . '-epi'; @endphp
                                    <div class="card mb-3 border-info epi-card">
                                        <div class="card-header bg-gradient-info text-white" 
                                             data-toggle="collapse" 
                                             data-target="#{{ $epiKey }}"
                                             role="button"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <i class="fas fa-chart-line mr-2"></i>
                                                    <strong style="font-size: 1rem;">EPIDEMIOLOGÍA</strong>
                                                    <span class="r-chip r-chip--blanco ml-2" title="Muestras de microbiología del paciente">
                                                        <i class="fas fa-vial mr-1"></i><b>{{ count($info['seguimientos']) }}</b>&nbsp;{{ count($info['seguimientos']) == 1 ? 'muestra' : 'muestras' }}
                                                    </span>
                                                    <span class="r-chip js-chip-estado ml-2 {{ $info['epi_completo'] ? 'r-chip--ok' : 'r-chip--pend' }}"
                                                          title="{{ $info['epi_completo'] ? 'Todos los microorganismos registrados' : 'Faltan microorganismos por registrar' }}">
                                                        <i class="fas {{ $info['epi_completo'] ? 'fa-check' : 'fa-exclamation-circle' }} mr-1"></i>{{ $info['epi_completo'] ? 'Completo' : 'Falta registrar' }}
                                                    </span>
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

                                                {{-- Barra de acción: agrupar las muestras seleccionadas en un mismo caso --}}
                                                <div class="agrupar-bar" data-paciente="{{ $paciente?->identificador_unico }}">
                                                    <div class="agrupar-info">
                                                        <i class="fas fa-object-group mr-1"></i>
                                                        Marca las muestras que sean del mismo caso y agrúpalas.
                                                        <span class="agrupar-conteo text-muted"></span>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-agrupar-casos" disabled>
                                                        <i class="fas fa-layer-group mr-1"></i> Agrupar seleccionadas
                                                    </button>
                                                </div>

                                                {{-- Acordeón: un bloque por CASO de microorganismo. El agrupamiento
                                                     lo define caso_id (que la usuaria puede rehacer con los checkboxes);
                                                     si por alguna razón falta, se agrupa por nombre de microorganismo. --}}
                                                {{-- $gruposMicro se calcula arriba, en el bloque del paciente, porque
                                                     la cabecera necesita sus conteos antes de llegar hasta aquí. --}}
                                                @foreach($gruposMicro as $microNombre => $grupoMicro)
                                                    @php
                                                        $microKey = $epiKey . '-m' . md5($microNombre);
                                                        // Registro representativo del grupo: sobre él se guardan los
                                                        // datos complementarios (que son iguales para todo el grupo).
                                                        $reg = $grupoMicro->first();
                                                    @endphp
                                                    <div class="card mb-2 border-secondary micro-card">
                                                        <div class="card-header bg-secondary text-white"
                                                             data-toggle="collapse"
                                                             data-target="#{{ $microKey }}"
                                                             role="button"
                                                             style="cursor: pointer;">
                                                            @php $microRegistrado = collect($grupoMicro)->every(fn ($r) => (bool) $r->registrado); @endphp
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center flex-wrap">
                                                                    <i class="fas fa-vial mr-2"></i>
                                                                    <strong>{{ $reg->microorganismo ?: 'Sin microorganismo' }}</strong>
                                                                    <span class="r-chip r-chip--blanco ml-2" title="Registros de este microorganismo">
                                                                        <b>{{ $grupoMicro->count() }}</b>&nbsp;{{ $grupoMicro->count() == 1 ? 'registro' : 'registros' }}
                                                                    </span>
                                                                    <span class="r-chip js-chip-estado ml-2 {{ $microRegistrado ? 'r-chip--ok' : 'r-chip--pend' }}"
                                                                          title="{{ $microRegistrado ? 'Registrado' : 'Falta por registrar' }}">
                                                                        <i class="fas {{ $microRegistrado ? 'fa-check' : 'fa-exclamation-circle' }} mr-1"></i>{{ $microRegistrado ? 'Registrado' : 'Sin registrar' }}
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
                                                                    @php $bloqueadoInfo = !optional(auth()->user())->puedeEditarEpidemiologia() && $reg->edicion_bloqueada; @endphp
                                                                    @if($bloqueadoInfo)
                                                                        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:0.8rem;">
                                                                            <i class="fas fa-lock mr-1"></i> Este formulario ya fue registrado. No tienes permiso para modificarlo.
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
                                                                <select name="registros[{{ $fila->id }}][tipo_muestra]" class="form-control form-control-sm">
                                                                    <option value="">— Seleccionar —</option>
                                                                    @foreach($conValor($catalogos['tiposMuestra']->pluck('descripcion')->all(), $fila->tipo_muestra) as $op)
                                                                        <option value="{{ $op }}" {{ ($fila->tipo_muestra ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
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
                                                            <div class="col-md-3">
                                                                <label class="proa-label">Fecha de Reporte <span class="text-danger">*</span></label>
                                                                <input type="date" name="registros[{{ $fila->id }}][fecha_reporte]"
                                                                       class="form-control form-control-sm fecha-reporte" required
                                                                       value="{{ $fila->fecha_reporte?->format('Y-m-d') }}">
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
                                                                   list="lista-diagnosticos" autocomplete="off" placeholder="Busque por código o texto…"
                                                                   value="{{ $reg->diagnostico_ingreso ?? '' }}">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 2: Tipo ID, Fecha de ingreso, Asegurador y Peso (constantes del paciente) --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-2">
                                                            <label class="proa-label">Tipo ID</label>
                                                            <select name="tipo_id" class="form-control form-control-sm const-field">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor(['ARG', 'AS', 'BOL', 'BRA', 'CC', 'CHI', 'CN', 'ECU', 'PAR', 'PER', 'PT', 'RC', 'RN', 'SC', 'TI', 'URG', 'VEN'], $reg->tipo_id) as $op)
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
                                                            <input type="number" step="0.1" min="0" inputmode="decimal" name="peso"
                                                                   class="form-control form-control-sm const-field solo-numero"
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
                                                                @foreach($conValor($opcionesSitio, $reg->sitio) as $op)
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

                                                    {{-- Fila 4b: Fechas de infección (req. 3) --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <label class="proa-label">Fecha de Dx. de infección</label>
                                                            <input type="date" name="fecha_dx_infeccion" class="form-control form-control-sm fecha-dx-infeccion"
                                                                   value="{{ isset($reg->fecha_dx_infeccion) ? $reg->fecha_dx_infeccion->format('Y-m-d') : '' }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="proa-label">DÍAS DE ESTANCIA PREVIOS A INFECCIÓN</label>
                                                            <input type="text" name="dias_estancia_previos_infeccion" class="form-control form-control-sm bg-white" readonly
                                                                   value="{{ $reg->dias_estancia_previos_infeccion ?? '' }}" placeholder="Calculado automáticamente">
                                                        </div>
                                                    </div>

                                                    {{-- Fila 5: Especialidad que realizo cirugia, Procedimiento quirurjico, Tiempo quirurjico --}}
                                                    <div class="row mb-2">
                                                        <div class="col-md-4">
                                                            <label class="proa-label">Especialidad que realizó cirugía</label>
                                                            <select name="especialidad_cirugia" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor($opcionesEspecialidadQx, $reg->especialidad_cirugia) as $op)
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
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" min="0" step="1" inputmode="numeric" name="tiempo_quirurgico"
                                                                       class="form-control form-control-sm solo-entero"
                                                                       value="{{ preg_replace('/\D/', '', (string) ($reg->tiempo_quirurgico ?? '')) }}"
                                                                       placeholder="Ej: 120">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">minutos</span>
                                                                </div>
                                                            </div>
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
                                                                @foreach($conValor($opcionesAntibioticos, $reg->antibioticos_usados) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->antibioticos_usados ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="proa-label">ASA Preoperatoria</label>
                                                            <select name="asa_preoperatoria" class="form-control form-control-sm">
                                                                <option value="">— Seleccionar —</option>
                                                                @foreach($conValor($opcionesAsa, $reg->asa_preoperatoria) as $op)
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
                                                                @foreach($conValor(['SI', 'NO', 'NO APLICA'], $reg->interconsulta_infectologia) as $op)
                                                                    <option value="{{ $op }}" {{ ($reg->interconsulta_infectologia ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- Fila 8: Comentarios (las fechas de inserción/retiro se movieron al bloque de enfermería, req. 7) --}}
                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <label class="proa-label">Comentarios</label>
                                                            <textarea name="comentarios" class="form-control form-control-sm" rows="2" placeholder="Comentarios adicionales...">{{ $reg->comentarios ?? '' }}</textarea>
                                                        </div>
                                                    </div>

                                                    @if($esEnfermero)
                                                    {{-- ===== Datos de enfermería (req. 7, 8, 11) — solo perfil enfermero ===== --}}
                                                    <div class="enfermeria-bloque border rounded p-2 mb-3">
                                                        <div class="proa-label font-weight-bold mb-2" style="color:#2a377e;">
                                                            <i class="fas fa-user-nurse mr-1"></i> Datos de enfermería
                                                        </div>

                                                        {{-- Req. 7: dispositivo de notificación obligatoria --}}
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label class="proa-label">¿Es una infección asociada a dispositivo de notificación obligatoria?</label>
                                                                <select name="dispositivo_notificacion" class="form-control form-control-sm select-dispositivo">
                                                                    <option value="">— Seleccionar —</option>
                                                                    @foreach($conValor(['SI', 'NO'], $reg->dispositivo_notificacion) as $op)
                                                                        <option value="{{ $op }}" {{ ($reg->dispositivo_notificacion ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2 bloque-dispositivo">
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
                                                        </div>

                                                        {{-- Req. 8: ¿ISO? --}}
                                                        <div class="row mb-1">
                                                            <div class="col-md-6">
                                                                <label class="proa-label">¿Este microorganismo corresponde a una ISO (Infección de Sitio Quirúrgico)?</label>
                                                                <select name="es_iso" class="form-control form-control-sm select-iso">
                                                                    <option value="">— Seleccionar —</option>
                                                                    @foreach($conValor(['SI', 'NO'], $reg->es_iso) as $op)
                                                                        <option value="{{ $op }}" {{ ($reg->es_iso ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 d-flex align-items-end bloque-iso-nota">
                                                                <small class="text-muted">Si es <strong>SÍ</strong>, diligencie los campos quirúrgicos de arriba (categoría, especialidad, procedimiento, baño, asepsia, profilaxis, antibióticos, ASA, tipo/clasificación de cirugía, NNIS). No se vuelven a preguntar.</small>
                                                            </div>
                                                        </div>

                                                        {{-- Req. 11: solo cuando SITIO ≠ "No aplica" --}}
                                                        <div class="row mb-1 bloque-sitio-enfermero">
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Duda</label>
                                                                <select name="duda" class="form-control form-control-sm">
                                                                    <option value="">—</option>
                                                                    @foreach($conValor(['SI', 'NO'], $reg->duda) as $op)
                                                                        <option value="{{ $op }}" {{ ($reg->duda ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="proa-label">Estado</label>
                                                                <select name="estado" class="form-control form-control-sm">
                                                                    <option value="">—</option>
                                                                    @foreach($conValor(['APROBADO', 'DESCARTADO'], $reg->estado) as $op)
                                                                        <option value="{{ $op }}" {{ ($reg->estado ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="proa-label">Modificado</label>
                                                                <select name="modificado" class="form-control form-control-sm">
                                                                    <option value="">—</option>
                                                                    @foreach($conValor(['SI', 'NO'], $reg->modificado) as $op)
                                                                        <option value="{{ $op }}" {{ ($reg->modificado ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label class="proa-label">Fecha de reporte al hospital seguro</label>
                                                                <input type="date" name="fecha_reporte_hospital_seguro" class="form-control form-control-sm"
                                                                       value="{{ isset($reg->fecha_reporte_hospital_seguro) ? $reg->fecha_reporte_hospital_seguro->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

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
                                    <div class="card mb-2 border-success proa-card">
                                        <div class="card-header text-white {{ $info['proa_completo'] ? 'bg-proa-ok' : 'bg-proa-pend' }}"
                                             data-toggle="collapse"
                                             data-target="#{{ $pacienteKey }}-proa"
                                             role="button"
                                             style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center flex-wrap">
                                                    <i class="fas fa-capsules mr-2"></i>
                                                    <strong style="font-size: 1rem;">PROA</strong>
                                                    <span class="r-chip r-chip--blanco ml-2" title="Medicamentos con seguimiento">
                                                        <b>{{ $info['total_medic'] }}</b>&nbsp;{{ $info['total_medic'] == 1 ? 'medicamento' : 'medicamentos' }}
                                                    </span>
                                                    <span class="r-chip js-chip-estado ml-2 {{ $info['proa_completo'] ? 'r-chip--ok' : 'r-chip--pend' }}"
                                                          title="{{ $info['proa_completo'] ? 'Todos los antibióticos registrados' : 'Faltan antibióticos por registrar' }}">
                                                        <i class="fas {{ $info['proa_completo'] ? 'fa-check' : 'fa-exclamation-circle' }} mr-1"></i>{{ $info['proa_completo'] ? 'Completo' : 'Falta registrar' }}
                                                    </span>
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
                                            // Los cursos de 7 días y el estado de este medicamento ya se
                                            // calcularon en el bloque del paciente, para el resumen.
                                            $estadoMed     = $estadoMedicamentos[$medicamento];
                                            $cursos        = $estadoMed['cursos'];
                                            $totalDosis    = $estadoMed['total'];
                                            $medRegistrado = $estadoMed['registrado'];
                                        @endphp

                                        {{-- Tarjeta de medicamento --}}
                                        <div class="card med-card mb-2">
                                            <div class="card-header med-header"
                                                 data-toggle="collapse"
                                                 data-target="#{{ $medKey }}"
                                                 aria-expanded="false"
                                                 role="button">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-pills text-success mr-1"></i>
                                                        <strong>{{ $medicamento }}</strong>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="r-chip r-chip--neut mr-2">
                                                            <b>{{ $totalDosis }}</b>&nbsp;{{ $totalDosis == 1 ? 'curso' : 'cursos' }}
                                                        </span>
                                                        <span class="r-chip js-chip-estado mr-2 {{ $medRegistrado ? 'r-chip--ok' : 'r-chip--pend' }}"
                                                              title="{{ $medRegistrado ? 'Registrado' : 'Falta por registrar' }}">
                                                            <i class="fas {{ $medRegistrado ? 'fa-check' : 'fa-exclamation-circle' }} mr-1"></i>{{ $medRegistrado ? 'Registrado' : 'Sin registrar' }}
                                                        </span>
                                                        <i class="fas fa-chevron-down collapse-icon text-muted"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Registros individuales del medicamento --}}
                                            <div id="{{ $medKey }}" class="collapse">
                                                <div class="card-body p-2 bg-white">
                                                    {{-- Un bloque por CURSO de tratamiento (7 días). Dentro de un
                                                         curso, las dosis con la misma fecha se muestran una sola vez. --}}
                                                    @foreach($cursos as $curso)
                                                        @php
                                                            $registro   = $curso['representativa'];
                                                            $inicio     = $curso['inicio'];
                                                            $fechaCard  = $medKey . '-curso-' . $registro->id;
                                                            $regKey     = $fechaCard;
                                                            $fechaLabel = $inicio ? $inicio->format('d/m/Y') : 'Sin fecha';
                                                            $diaActual  = \App\Support\ProaCursos::diaActual($inicio);
                                                            $dosisCurso = $curso['dosis']->count();
                                                        @endphp

                                                        {{-- Un bloque por curso: fecha de inicio + contador Día X de 7 --}}
                                                        <div class="card reg-card mb-2">
                                                            <div class="card-header reg-header"
                                                                 data-toggle="collapse"
                                                                 data-target="#{{ $fechaCard }}"
                                                                 aria-expanded="false"
                                                                 role="button">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <i class="fas fa-calendar-day text-info mr-1"></i>
                                                                        <strong>{{ $fechaLabel }}</strong>
                                                                        @if($inicio)
                                                                            <span class="badge curso-contador {{ $diaActual > 7 ? 'badge-warning' : 'badge-info' }} ml-2"
                                                                                  data-inicio="{{ $inicio->format('Y-m-d') }}"
                                                                                  title="Día de tratamiento (lo ideal son 7 días)">
                                                                                Día {{ $diaActual }} de 7
                                                                            </span>
                                                                        @endif
                                                                        @if($dosisCurso > 1)
                                                                            <span class="badge badge-light border ml-1" style="font-size:0.7rem;" title="Dosis dentro de este curso">{{ $dosisCurso }} dosis</span>
                                                                        @endif
                                                                    </div>
                                                                    <i class="fas fa-chevron-down collapse-icon text-muted"></i>
                                                                </div>
                                                            </div>

                                                            <div id="{{ $fechaCard }}" class="collapse">
                                                                <div class="card-body bg-white p-2">
                                                                        <div class="proa-dose">
                                                                @php $interv = $intervenciones[$registro->id] ?? null; @endphp

                                                                <form class="proa-form" data-id="{{ $registro->id }}" data-registrado="{{ $intervenciones->has($registro->id) ? '1' : '0' }}">
                                                                @csrf

                                                                <input type="hidden" name="id_deta_procedimiento" value="{{ $registro->id }}">
                                                                @php $bloqueadoProa = !optional(auth()->user())->puedeEditarProa() && optional($interv)->edicion_bloqueada; @endphp
                                                                @if($bloqueadoProa)
                                                                    <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:0.8rem;">
                                                                        <i class="fas fa-lock mr-1"></i> Esta intervención PROA ya fue registrada. No tienes permiso para modificarla.
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
                                                                        <select class="form-control form-control-sm" name="via_aplicacion">
                                                                            <option value="">— Seleccionar —</option>
                                                                            @foreach($conValor(['Oral', 'Venoso'], $registro->Via_Aplicacion) as $op)
                                                                                <option value="{{ $op }}" {{ ($registro->Via_Aplicacion ?? '') == $op ? 'selected' : '' }}>{{ $op }}</option>
                                                                            @endforeach
                                                                        </select>
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
                                                                        <input type="text" class="form-control form-control-sm bg-white tiempo-tratamiento-auto" readonly
                                                                               name="tiempo_tratamiento"
                                                                               data-inicio="{{ $registro->Fec_Sumistro ? \Carbon\Carbon::parse($registro->Fec_Sumistro)->format('Y-m-d') : '' }}"
                                                                               value="{{ $interv?->tiempo_tratamiento ?? '' }}"
                                                                               title="Contador automático desde la fecha de inicio (ideal 7 días)"
                                                                               placeholder="Automático">
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
                                                                        </div>{{-- /.proa-dose --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- /bloque de fecha --}}
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
    </div>{{-- /#registros-resultados --}}

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

        /* Bloque de campos exclusivos del perfil enfermero (req. 7, 8, 11) */
        .enfermeria-bloque { background: #f7f9fd; border-color: #e5e7f0 !important; }

        /* ── FIX: encabezados de bloques con COLOR DISTINTIVO PASTEL + texto oscuro.
              (El estilo shadowless los dejaba en blanco y el texto blanco quedaba
              invisible.) Especificidad alta para ganarle a proahuv-crud.css. ── */
        .content-wrapper .card.epi-card > .card-header {
            background: #e7f3fb !important;
            color: #1a6b8e !important;
            border-bottom: 1px solid #cfe6f4 !important;
        }
        .content-wrapper .card.micro-card > .card-header {
            background: #eceef4 !important;
            color: #495264 !important;
            border-bottom: 1px solid #dfe2ea !important;
        }
        .content-wrapper .card.proa-card > .card-header.bg-proa-ok {
            background: #e6f6ee !important;
            color: #1c7a4d !important;
            border-bottom: 1px solid #cdeede !important;
        }
        .content-wrapper .card.proa-card > .card-header.bg-proa-pend {
            background: #fdeceb !important;
            color: #b23b46 !important;
            border-bottom: 1px solid #f6d6d6 !important;
        }
        /* El texto interno (strong, small, íconos, badge-light) hereda el color oscuro */
        .content-wrapper .card.epi-card > .card-header .badge-light,
        .content-wrapper .card.micro-card > .card-header .badge-light,
        .content-wrapper .card.proa-card > .card-header .badge-light {
            background: rgba(255, 255, 255, 0.75) !important;
        }
        /* Bordes de acento pastel para reforzar la distinción entre bloques */
        .content-wrapper .card.epi-card   { border-left: 3px solid #8ec7e8 !important; }
        .content-wrapper .card.micro-card { border-left: 3px solid #c1c7d4 !important; }
        .content-wrapper .card.proa-card  { border-left: 3px solid #86d3a8 !important; }

        /* Campo inhabilitado por la lógica de SITIO = "50. No aplica" (req. 2.2) */
        .campo-inhabilitado { opacity: .5; }
        .campo-inhabilitado .form-control,
        .campo-inhabilitado .input-group-text { background-color: #f1f2f6 !important; cursor: not-allowed; }

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

        /* Barra para agrupar muestras seleccionadas en un mismo caso */
        .agrupar-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            background: #eef1fa;
            border: 1px solid #dbe1f3;
            border-radius: 10px;
            padding: 8px 14px;
            margin-bottom: 12px;
        }
        .agrupar-info {
            font-size: 0.82rem;
            color: #2a377e;
            font-weight: 600;
        }
        .agrupar-conteo {
            font-weight: 500;
            margin-left: 6px;
        }

        /* ── Grid de servicios: 2 → 3 → 4 → 5 columnas según el ancho ── */
        .col-servicio {
            position: relative;
            width: 100%;
            padding-right: 12px;
            padding-left: 12px;
            flex: 0 0 50%;              /* móvil: 2 por fila */
            max-width: 50%;
        }
        @media (min-width: 768px)  { .col-servicio { flex: 0 0 33.3333%; max-width: 33.3333%; } }  /* md: 3 */
        @media (min-width: 1200px) { .col-servicio { flex: 0 0 25%;      max-width: 25%; } }        /* ancho: 4 */

        /* ── Tiles de servicio: cards cuadradas, grid limpio y moderno ── */
        .service-tile {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 162px;
            background: #2a377e;              /* azul institucional plano */
            color: #ffffff;
            border-radius: 16px;
            padding: 16px 16px 14px;
            transition: transform 0.16s ease, background-color 0.16s ease;
        }
        .service-tile:hover {
            background: #212a63;
            color: #ffffff;
            transform: translateY(-3px);
            text-decoration: none;
        }
        .service-tile-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .service-tile-ico {
            width: 40px; height: 40px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.14);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            color: #ffffff;
        }
        .service-tile-arrow {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.85rem;
        }
        .service-tile-name {
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.3;
            color: #ffffff;
            margin: 0 0 auto;                /* empuja las stats al fondo */
            display: -webkit-box;
            -webkit-line-clamp: 2;           /* máx. 2 líneas */
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .service-tile-stats {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
        }
        .service-tile-pac {
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.82);
            white-space: nowrap;
        }
        .service-tile-proa {
            font-size: 0.72rem;
            font-weight: 700;
            background: #2fbf71;
            color: #ffffff;
            padding: 2px 9px;
            border-radius: 999px;
            white-space: nowrap;
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

        /* Dosis dentro de un mismo día (cuando hay más de una) */
        .dosis-tag {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #2e7d5b;
            background: #eef6f1;
            border-radius: 8px;
            padding: 4px 10px;
            display: inline-block;
            margin: 4px 0 8px;
        }
        .proa-dose {
            padding: 2px 4px;
        }

        /* La semaforizacion de estados vive ahora en public/css/registros.css:
           chips .r-chip--ok / .r-chip--pend, marcados con .js-chip-estado. */

        /* Fondo del bloque global de PROA según su estado */
        .bg-proa-ok   { background: #2e7d5b; }   /* verde: todo registrado */
        .bg-proa-pend { background: #b23b46; }   /* rojo: falta por registrar */

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

        /* ============================================================
           REFRESCO MINIMALISTA — estilo de la página de login
           (blanco limpio, azul institucional #2a377e, bordes suaves)
           ============================================================ */
        /* Fondo neutro y limpio SOLO en esta página (como el login),
           sin la imagen del hospital que resta minimalismo. */
        body, .wrapper {
            background: #f4f6fb !important;
        }
        .content-wrapper, .content-wrapper .content { background: transparent !important; }
        /* Todas las tarjetas: blanco nítido con borde limpio (sin efecto "vidrio") */
        .content-wrapper .card {
            background: #ffffff !important;
            border: 1px solid #e7e9f2 !important;
        }

        /* Tarjetas contenedoras (buscador, etc.): blanco, redondeado, SIN sombra */
        .content-wrapper .card.card-outline,
        .content-wrapper .card.card-primary {
            border: 1px solid #e7e9f2 !important;
            border-top: 1px solid #e7e9f2 !important;
            border-radius: 16px !important;
            box-shadow: none !important;
        }
        /* Encabezado claro solo para tarjetas contenedoras, NO para las de servicio
           (esas conservan su encabezado azul con texto blanco). */
        .content-wrapper .card.card-outline:not(.service-card) > .card-header {
            border-bottom: 1px solid #eef0f5;
            background: #fbfcfe;
            border-radius: 16px 16px 0 0;
        }

        /* Inputs y selects — como el login (borde suave, foco azul) */
        .form-control {
            border: 1px solid #dfe2ec;
            border-radius: 10px;
        }
        .form-control:focus {
            border-color: #2f6fed;
            box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12);
        }
        select.form-control { border-radius: 10px; }

        /* Buscador: ícono e input integrados y limpios */
        .input-group-text.bg-primary {
            background: #2a377e !important;
            border-color: #2a377e !important;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control { border-radius: 0; }
        .input-group > .input-group-prepend > .input-group-text { border-radius: 10px 0 0 10px; }
        .input-group > .input-group-append > .btn:last-child,
        .input-group > .input-group-append > .btn { border-radius: 0 10px 10px 0; }

        /* Botones primarios — azul institucional, redondeados como login */
        .btn-primary {
            background: #2a377e !important;
            border-color: #2a377e !important;
            border-radius: 10px;
        }
        .btn-primary:hover { background: #212a63 !important; border-color: #212a63 !important; }
        .btn { border-radius: 10px; }
        .btn-sm { border-radius: 9px; }
        .btn-outline-primary { color: #2a377e; border-color: #c9d1ea; }
        .btn-outline-primary:hover { background: #2a377e; border-color: #2a377e; }

        /* Tarjetas de servicio — más suaves y elegantes; encabezado AZUL con
           texto blanco (se refuerza para que no lo pise ninguna regla). */
        .service-card { border-radius: 16px; border-color: #e7e9f2 !important; overflow: hidden; }
        .content-wrapper .service-card > .card-header.service-header {
            background: #2a377e !important;
            color: #fff !important;
            border-radius: 16px 16px 0 0;
        }
        .content-wrapper .service-card > .card-header.service-header:hover {
            background: #212a63 !important;
        }
        .service-card .service-info h6,
        .service-card .service-icon i,
        .service-card .service-header i { color: #ffffff !important; }

        /* Tarjeta de paciente — cabecera BLANCA limpia (como login) */
        .patient-card {
            border-radius: 16px;
            border-color: #e7e9f2;
            box-shadow: none;
        }
        .patient-card > .card-header {
            background: #ffffff !important;
            border-left: 4px solid #2a377e;
            border-radius: 16px 16px 0 0;
        }
        .patient-header:hover { background: #f7f9fd !important; }

        /* Bloques internos: bordes suaves y esquinas parejas */
        .med-card, .reg-card { border-radius: 12px; border-color: #ecedf4; }
        .med-card > .card-header, .reg-card > .card-header { background: #fbfcfe; }

        /* Barra de scroll de pacientes — discreta */
        .pacientes-scroll-container::-webkit-scrollbar-thumb { background: #cbd3ea; }
        .pacientes-scroll-container::-webkit-scrollbar-thumb:hover { background: #2a377e; }
        .pacientes-scroll-container::-webkit-scrollbar-track { background: transparent; }

        /* ── Paginación rediseñada (minimalista, píldoras) ── */
        .pagination { gap: 0; flex-wrap: wrap; }
        .pagination .page-item { margin: 0 3px; }
        .pagination .page-item .page-link {
            border: 1px solid #e5e7f0;
            border-radius: 10px;
            color: #454b56;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.88rem;
            font-weight: 600;
            background: #ffffff;
            margin: 0;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }
        .pagination .page-item .page-link:hover {
            background: #eef1fa;
            color: #2a377e;
            border-color: #dbe1f3;
            transform: translateY(-1px);
        }
        .pagination .page-item.active .page-link {
            background: #2a377e;
            border-color: #2a377e;
            color: #ffffff;
            box-shadow: none;
        }
        .pagination .page-item.disabled .page-link {
            color: #c3c9d4;
            background: #ffffff;
            border-color: #eef0f5;
            box-shadow: none;
        }
        .pagination .page-link:focus { box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.15); }
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

            // Inputs solo numéricos: entero (tiempo quirúrgico) y decimal (peso).
            $(document).on('input', '.solo-entero', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            $(document).on('input', '.solo-numero', function () {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            });

            // ── Semáforo en vivo (sin recargar) ─────────────────────────────
            // Cada cabecera lleva un chip de estado marcado con .js-chip-estado.
            // Pasar de "pendiente" a "registrado" es cambiar su modificador, su
            // icono y su texto; el conteo de pendientes se hace sobre esos chips.
            function chipRegistrado($chip, texto, titulo) {
                if (!$chip || !$chip.length) { return; }
                $chip.removeClass('r-chip--pend r-chip--avi')
                     .addClass('r-chip--ok')
                     .attr('title', titulo || 'Registrado')
                     .html('<i class="fas fa-check mr-1"></i>' + (texto || 'Registrado'));
            }

            // El paciente queda completo cuando no queda ningún chip pendiente dentro
            // de su tarjeta (sin contar el suyo propio).
            // Mantiene al día la línea de resumen: los conteos se releen de los
            // propios chips, así no hay dos fuentes de verdad.
            function pintarFalta($span, n, texto) {
                if (!$span.length) { return; }
                if (n === 0) { $span.hide(); }
                else { $span.text(' · ' + n + ' ' + texto).show(); }
            }
            function actualizarResumen($patient) {
                if (!$patient || !$patient.length) { return; }
                var microPend = $patient.find('.micro-card > .card-header .js-chip-estado.r-chip--pend').length;
                var medPend   = $patient.find('.med-card > .card-header .js-chip-estado.r-chip--pend').length;

                if (microPend === 0) {
                    $patient.find('.js-resumen-epi')
                            .removeClass('r-resumen-item--pend').addClass('r-resumen-item--ok')
                            .find('i').attr('class', 'fas fa-check-circle');
                }
                pintarFalta($patient.find('.js-resumen-micro-falta'), microPend, 'sin registrar');
                pintarFalta($patient.find('.js-resumen-proa-falta'), medPend, 'sin intervención');

                var total = microPend + medPend;
                var $chip = $patient.find('.js-chip-paciente');
                if (total > 0 && $chip.hasClass('r-chip--pend')) {
                    $chip.html('<i class="fas fa-exclamation-circle mr-1"></i>' +
                               total + (total === 1 ? ' pendiente' : ' pendientes'));
                }
            }


            function actualizarEstadoPaciente($patient) {
                if (!$patient || !$patient.length) { return; }
                actualizarResumen($patient);
                var pendientes = $patient.find('.js-chip-estado.r-chip--pend')
                                         .not('.js-chip-paciente').length;
                if (pendientes === 0) {
                    chipRegistrado($patient.find('.js-chip-paciente'), 'Completo', 'Paciente completo');
                    $patient.children('.card-header')
                            .removeClass('r-rail-pend').addClass('r-rail-ok');
                }
            }

            // Epidemiología: al guardar un bloque de microorganismo se marca como
            // registrado; si ya no queda ninguno pendiente, el bloque global también.
            function marcarEpiRegistrado($form) {
                var $micro = $form.closest('.micro-card');
                chipRegistrado($micro.children('.card-header').find('.js-chip-estado'), 'Registrado');

                var $epi = $form.closest('.epi-card');
                if ($epi.find('.micro-card > .card-header .js-chip-estado.r-chip--pend').length === 0) {
                    chipRegistrado($epi.children('.card-header').find('.js-chip-estado'),
                                   'Completo', 'Todos los microorganismos registrados');
                }
                actualizarEstadoPaciente($form.closest('.patient-card'));
            }

            // PROA: al guardar una intervención esa dosis queda registrada; el
            // antibiótico se marca si todas sus dosis lo están, y el bloque global
            // si ya no queda ninguna pendiente.
            function marcarProaRegistrado($form) {
                $form.attr('data-registrado', '1');

                var $med = $form.closest('.med-card');
                if ($med.find('.proa-form[data-registrado="0"]').length === 0) {
                    chipRegistrado($med.children('.card-header').find('.js-chip-estado'), 'Registrado');
                }

                var $proa = $form.closest('.proa-card');
                if ($proa.find('.proa-form[data-registrado="0"]').length === 0) {
                    var $ph = $proa.children('.card-header');
                    $ph.removeClass('bg-proa-pend').addClass('bg-proa-ok');
                    chipRegistrado($ph.find('.js-chip-estado'), 'Completo', 'Todos los antibióticos registrados');
                }
                actualizarEstadoPaciente($form.closest('.patient-card'));
            }

            // Guardar formulario "Información adicional de (microorganismo)" via AJAX
            $(document).on('click', '.btn-registrar-info', function () {
                var $btn = $(this);
                var $form = $btn.closest('.microorganismo-info-form');

                // FECHA DE REPORTE es obligatoria en cada registro de la muestra (req. 9).
                var $faltantes = $form.find('.fecha-reporte').filter(function () { return !this.value; });
                if ($faltantes.length) {
                    if (window.Swal) {
                        Swal.fire({ icon: 'warning', title: 'Falta la Fecha de Reporte',
                            text: 'La Fecha de Reporte es obligatoria en todos los registros de la muestra.' });
                    } else {
                        alert('La Fecha de Reporte es obligatoria en todos los registros de la muestra.');
                    }
                    $faltantes.first().focus();
                    return;
                }

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
                            marcarEpiRegistrado($form);   // semáforo en vivo
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
                            marcarProaRegistrado($form);   // semáforo en vivo
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

            // Llenar departamentos y, si hay valor guardado, sus municipios.
            // Se puede re-ejecutar tras un intercambio de contenido por AJAX.
            function inicializarDane() {
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
            }
            inicializarDane();

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
                aplicarLogicaSitio($form);
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

            // Resaltar la sección de campos cuyo checkbox está marcado y
            // actualizar el estado de la barra "Agrupar seleccionadas".
            $(document).on('change', '.registro-check', function () {
                $(this).closest('.registro-muestra').toggleClass('is-selected', this.checked);
                actualizarBarraAgrupar($(this).closest('.patient-card'));
            });

            function actualizarBarraAgrupar($scope) {
                if (!$scope || !$scope.length) { return; }
                var n = $scope.find('.registro-check:checked').length;
                var $bar = $scope.find('.agrupar-bar');
                $bar.find('.agrupar-conteo').text(n > 0 ? '(' + n + ' seleccionada' + (n === 1 ? '' : 's') + ')' : '');
                // Se necesita al menos 2 muestras para formar/mover un caso.
                $bar.find('.btn-agrupar-casos').prop('disabled', n < 2);
            }

            // Agrupar en un mismo caso todas las muestras marcadas del paciente.
            $(document).on('click', '.btn-agrupar-casos', function () {
                var $btn   = $(this);
                var $scope = $btn.closest('.patient-card');
                var ids = $scope.find('.registro-check:checked').map(function () {
                    return this.value;
                }).get();

                if (ids.length < 2) { return; }

                if (!confirm('Se agruparán ' + ids.length + ' muestra(s) como un mismo caso de microorganismo. ¿Continuar?')) {
                    return;
                }

                var token = $('meta[name="csrf-token"]').attr('content')
                          || $scope.find('[name="_token"]').first().val();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Agrupando...');

                $.ajax({
                    url: '{{ route("epidemiologia.casos.agrupar") }}',
                    method: 'POST',
                    data: { muestra_ids: ids },
                    headers: { 'X-CSRF-TOKEN': token },
                    success: function (resp) {
                        if (resp && resp.success) {
                            location.reload();
                        } else {
                            alert('No se pudo agrupar: ' + ((resp && resp.message) || 'error desconocido'));
                            $btn.prop('disabled', false).html('<i class="fas fa-layer-group mr-1"></i> Agrupar seleccionadas');
                        }
                    },
                    error: function (xhr) {
                        var m = (xhr.responseJSON && xhr.responseJSON.message) || 'Error al agrupar las muestras.';
                        alert(m);
                        $btn.prop('disabled', false).html('<i class="fas fa-layer-group mr-1"></i> Agrupar seleccionadas');
                    }
                });
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

            // Días de estancia previos a la infección (req. 3):
            // (fecha de Dx. de infección) − (fecha de ingreso hospitalario).
            $(document).on('change', '.microorganismo-info-form .fecha-dx-infeccion, .microorganismo-info-form [name="fecha_ingreso_hosp"]', function () {
                var $form = $(this).closest('.microorganismo-info-form');
                var fechaDxVal = $form.find('[name="fecha_dx_infeccion"]').val();
                var fechaIngresoVal = $form.find('[name="fecha_ingreso_hosp"]').val();
                var $diasInput = $form.find('[name="dias_estancia_previos_infeccion"]');

                if (!fechaDxVal || !fechaIngresoVal) {
                    $diasInput.val('');
                    return;
                }
                var diff = Math.round((new Date(fechaDxVal) - new Date(fechaIngresoVal)) / 86400000);
                $diasInput.val(isNaN(diff) ? '' : diff);
            });

            // Lógica condicional del campo SITIO (req. 2.2 / 2.3):
            // cuando SITIO = "50. No aplica" solo quedan habilitados TIPO,
            // CLASIFICACIÓN (+ texto), REVISIÓN CON EQUIPO, INTERCONSULTA y
            // COMENTARIOS. Los datos del paciente (const-field) y los de la
            // muestra siguen habilitados siempre. Los campos quirúrgicos y de
            // desenlace se inhabilitan para evitar errores de captura.
            var CAMPOS_QX_SITIO = [
                'fecha_quirurgica_previa', 'dias_entre_qx_e_infeccion', 'categoria_quirurgica', 'egreso',
                'especialidad_cirugia', 'procedimiento_quirurgico', 'tiempo_quirurgico', 'bano_quirurgico',
                'asepsia_quirurgica', 'profilaxis', 'antibioticos_usados', 'asa_preoperatoria',
                'tipo_cirugia', 'clasificacion_cirugia', 'puntaje_nnis'
            ];
            function aplicarLogicaSitio($form) {
                if (!$form || !$form.length) { return; }
                var sitio = ($form.find('[name="sitio"]').val() || '').trim();
                var noAplica = (sitio === '50. No aplica');
                CAMPOS_QX_SITIO.forEach(function (name) {
                    var $campo = $form.find('[name="' + name + '"]');
                    if (!$campo.length) { return; }
                    $campo.prop('disabled', noAplica);
                    $campo.closest('[class*="col-md-"]').toggleClass('campo-inhabilitado', noAplica);
                });

                // Req. 11: los campos de enfermería (Duda/Estado/Modificado/Fecha
                // reporte hospital seguro) solo se muestran cuando SITIO ≠ "No aplica".
                var mostrarSitioEnf = (sitio !== '' && !noAplica);
                $form.find('.bloque-sitio-enfermero').toggle(mostrarSitioEnf);
            }

            // Req. 7: las fechas de inserción/retiro solo aparecen si el dispositivo
            // de notificación obligatoria = "SI".
            function aplicarLogicaDispositivo($form) {
                if (!$form || !$form.length) { return; }
                var val = ($form.find('.select-dispositivo').val() || '').trim().toUpperCase();
                $form.find('.bloque-dispositivo').toggle(val === 'SI');
            }
            $(document).on('change', '.microorganismo-info-form .select-dispositivo', function () {
                aplicarLogicaDispositivo($(this).closest('.microorganismo-info-form'));
            });

            // Disparar cálculos para registros que ya tienen valores
            function dispararCalculos() {
                $('.microorganismo-info-form').each(function () {
                    var $form = $(this);
                    $form.find('select[name="tipo"]').trigger('change');
                    $form.find('[name="fecha_quirurgica_previa"]').trigger('change');
                    $form.find('.fecha-dx-infeccion').trigger('change');
                    aplicarLogicaSitio($form);
                    aplicarLogicaDispositivo($form);
                });
            }
            setTimeout(dispararCalculos, 500);

            // ============================================================
            // NAVEGACIÓN EN TIEMPO REAL (sin recargar la página)
            // Búsquedas, filtros, paginación y tarjetas de servicio se cargan
            // por AJAX intercambiando solo el contenedor de resultados.
            // ============================================================
            function reinitDinamico() {
                inicializarDane();
                actualizarContadoresTratamiento();
                setTimeout(dispararCalculos, 200);
            }

            // Contador de tratamiento en vivo: "Día X de 7" en el bloque del curso
            // y el número de días en el campo "Tiempo de tratamiento".
            function actualizarContadoresTratamiento() {
                var hoy = new Date(); hoy.setHours(0, 0, 0, 0);
                function diasDesde(ini) {
                    if (!ini) { return null; }
                    var d = new Date(ini + 'T00:00:00');
                    if (isNaN(d.getTime())) { return null; }
                    var n = Math.floor((hoy - d) / 86400000) + 1;
                    return n < 1 ? 1 : n;
                }
                $('.curso-contador').each(function () {
                    var n = diasDesde($(this).data('inicio'));
                    if (n === null) { return; }
                    $(this).text('Día ' + n + ' de 7')
                           .removeClass('badge-info badge-warning')
                           .addClass(n > 7 ? 'badge-warning' : 'badge-info');
                });
                $('.tiempo-tratamiento-auto').each(function () {
                    var n = diasDesde($(this).data('inicio'));
                    if (n === null) { return; }
                    this.value = n + (n === 1 ? ' día' : ' días');
                });
            }
            actualizarContadoresTratamiento();

            function cargarResultados(url, push) {
                var $cont = $('#registros-resultados');
                if (!$cont.length) { window.location = url; return; }
                $cont.css({ opacity: 0.45, 'pointer-events': 'none' });

                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (html) {
                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var nuevo = doc.getElementById('registros-resultados');
                        if (nuevo) {
                            $cont.html(nuevo.innerHTML).css({ opacity: '', 'pointer-events': '' });
                            if (push) { try { history.pushState({ url: url }, '', url); } catch (e) {} }
                            reinitDinamico();
                            $('html, body').animate({ scrollTop: $cont.offset().top - 80 }, 200);
                        } else {
                            window.location = url; // respaldo
                        }
                    },
                    error: function () {
                        window.location = url; // respaldo: recarga normal
                    }
                });
            }

            // Buscador y filtros (mismo formulario GET)
            $(document).on('submit', 'form[action="{{ route('registros.index') }}"]', function (e) {
                e.preventDefault();
                var base = '{{ route('registros.index') }}';
                var qs = $(this).serialize();
                cargarResultados(base + (qs ? '?' + qs : ''), true);
            });

            // La barra de contexto sigue al paciente abierto: así no hacen falta
            // dos cabeceras fijas apiladas (la del servicio y la del paciente).
            $(document).on('shown.bs.collapse show.bs.collapse', '.patient-card > .collapse', function () {
                var nombre = $(this).closest('.patient-card')
                                    .find('.patient-header strong').first().text().trim();
                $('.js-ctx-paciente').addClass('r-visible').find('b').text(nombre);
            });
            $(document).on('hidden.bs.collapse', '.patient-card > .collapse', function () {
                if ($('.patient-card > .collapse.show').length === 0) {
                    $('.js-ctx-paciente').removeClass('r-visible').find('b').text('');
                }
            });


            // Enlaces de navegación de la misma página (paginación, tarjetas de
            // servicio, "Volver a Servicios", limpiar filtros).
            $(document).on('click', '#registros-resultados a[href], a.js-nav[href]', function (e) {
                var href = $(this).attr('href') || '';
                if (!href || href.charAt(0) === '#' || $(this).attr('target')) { return; }
                if (href.indexOf('{{ route('registros.index') }}') === -1 && href.indexOf('?') === -1) { return; }
                e.preventDefault();
                cargarResultados(href, true);
            });

            // Botón atrás/adelante del navegador
            $(window).on('popstate', function () {
                cargarResultados(window.location.href, false);
            });
        });
    </script>
@stop
