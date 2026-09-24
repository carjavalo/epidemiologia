<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\EncabezadoProcedimiento;
use App\Models\EpidemiologiaRegistro;
use App\Models\Paciente;
use App\Models\Procedimiento;
use App\Support\Estandarizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeguimientoMicrobiologicoController extends Controller
{
    public function formularioImportacion()
    {
        return view('seguimiento.importar');
    }

    /** Extensiones aceptadas para los archivos de importación. */
    private const EXTENSIONES = ['txt', 'csv', 'xlsx', 'ods'];

    /**
     * Posición por defecto de cada campo de epidemiología (layout de la plantilla).
     * Solo se usa cuando el archivo NO trae fila de encabezados.
     */
    private const POSICIONES_EPI = [
        'nombre' => 0, 'identificador_unico' => 1, 'fecha_nacimiento' => 2, 'sexo' => 3,
        'id_historia' => 4, 'tipo_muestra' => 5, 'n_reporte' => 6, 'sede' => 7,
        'ubicacion' => 8, 'fecha_toma_muestra' => 9, 'microorganismo' => 10, 'cultivo_num' => 11,
        'sensibles' => 12, 'intermedios' => 13, 'resistentes' => 14, 'marcadores_resistencia' => 15,
        'pais_origen' => 16, 'departamento' => 17, 'municipio' => 18, 'diagnostico_ingreso' => 19,
        'asegurador' => 20, 'peso' => 21, 'fecha_ingreso_hosp' => 22, 'fecha_quirurgica_previa' => 23,
        'categoria_quirurgica' => 24, 'egreso' => 25, 'sitio' => 26, 'tipo' => 27,
        'clasificacion' => 28, 'especialidad_cirugia' => 29, 'procedimiento_quirurgico' => 30,
        'tiempo_quirurgico' => 31, 'bano_quirurgico' => 32, 'asepsia_quirurgica' => 33,
        'profilaxis' => 34, 'antibioticos_usados' => 35, 'asa_preoperatoria' => 36,
        'tipo_cirugia' => 37, 'clasificacion_cirugia' => 38, 'puntaje_nnis' => 39,
        'revision_equipo' => 40, 'interconsulta_infectologia' => 41, 'fecha_insercion' => 42,
        'fecha_retiro' => 43, 'comentarios' => 44,
    ];

    /**
     * Encabezado normalizado -> campo. Permite importar archivos con columnas en
     * distinto orden o cantidad. Los encabezados no listados aquí se ignoran.
     *
     * NOTA: "NACIONALIDAD" se ignora a propósito: en los históricos esa columna
     * contiene municipios sin depurar, no países.
     */
    private const MAPA_EPI = [
        'INFORMACION DEL PACIENTE' => 'nombre',
        'NOMBRE' => 'nombre',
        'ID DEL PACIENTE DOCUMENTO' => 'identificador_unico',
        'ID DEL PACIENTE' => 'identificador_unico',
        'DOCUMENTO' => 'identificador_unico',
        'FECHA NACIMIENTO' => 'fecha_nacimiento',
        'FECHA DE NACIMIENTO' => 'fecha_nacimiento',
        'SEXO' => 'sexo',
        'CODIGO PACIENTE HISTORIA CLINICA' => 'id_historia',
        'CODIGO DEL PACIENTE' => 'id_historia',
        'HISTORIA CLINICA' => 'id_historia',
        'TIPO Y ZONA MUESTRA' => 'tipo_muestra',
        'TIPO Y ZONA' => 'tipo_muestra',
        'TIPO DE MUESTRA' => 'tipo_muestra',
        'N DE ACCESO REPORTE' => 'n_reporte',
        'N DE ACCESO' => 'n_reporte',
        'ID CLIENTE MUESTRA SEDE' => 'sede',
        'ID DEL CLIENTE DE LA MUESTRA' => 'sede',
        'SEDE' => 'sede',
        'SERVICIO' => 'ubicacion',
        'UBICACION' => 'ubicacion',
        'FECHA TOMA MUESTRA' => 'fecha_toma_muestra',
        'FECHA TOMA DE MUESTRA' => 'fecha_toma_muestra',
        'FECHA' => 'fecha_toma_muestra',
        'ORGANISMO MICROORGANISMO' => 'microorganismo',
        'ORGANISMO' => 'microorganismo',
        'MICROORGANISMO' => 'microorganismo',
        'CULTIVO' => 'cultivo_num',
        'SENSIBLES' => 'sensibles',
        'FARMACOS SENSIBLES' => 'sensibles',
        'INTERMEDIOS' => 'intermedios',
        'FARMACOS INTERMEDIOS' => 'intermedios',
        'RESISTENTES' => 'resistentes',
        'FARMACOS RESISTENTES' => 'resistentes',
        'MARCADORES' => 'marcadores_resistencia',
        'ABREVIATURAS DE LOS MARCADORES DE RESISTENCIA' => 'marcadores_resistencia',
        // Complementarios
        'PAIS DE ORIGEN' => 'pais_origen',
        'DEPARTAMENTO' => 'departamento',
        'MUNICIPIO' => 'municipio',
        'DIAGNOSTICO DE INGRESO' => 'diagnostico_ingreso',
        'ASEGURADOR' => 'asegurador',
        'PESO KG' => 'peso',
        'PESO' => 'peso',
        'FECHA DE INGRESO' => 'fecha_ingreso_hosp',
        'FECHA QUIRURGICA PREVIA' => 'fecha_quirurgica_previa',
        'CATEGORIA QUIRURGICA' => 'categoria_quirurgica',
        'EGRESO' => 'egreso',
        'SITIO' => 'sitio',
        'TIPO' => 'tipo',
        'CLASIFICACION' => 'clasificacion',
        'ESPECIALIDAD QUE REALIZO CIRUGIA' => 'especialidad_cirugia',
        'PROCEDIMIENTO QUIRURGICO' => 'procedimiento_quirurgico',
        'TIEMPO QUIRURGICO' => 'tiempo_quirurgico',
        'BANO QUIRURGICO' => 'bano_quirurgico',
        'ASEPSIA QUIRURGICA' => 'asepsia_quirurgica',
        'PROFILAXIS' => 'profilaxis',
        'ANTIBIOTICOS USADOS' => 'antibioticos_usados',
        'ASA PREOPERATORIA' => 'asa_preoperatoria',
        'TIPO CIRUGIA' => 'tipo_cirugia',
        'CLASIFICACION CIRUGIA' => 'clasificacion_cirugia',
        'PUNTAJE NNIS' => 'puntaje_nnis',
        'REVISION CON EQUIPO' => 'revision_equipo',
        'INTERCONSULTA INFECTOLOGIA' => 'interconsulta_infectologia',
        'FECHA DE INSERCION' => 'fecha_insercion',
        'FECHA DE RETIRO' => 'fecha_retiro',
        'COMENTARIOS' => 'comentarios',
    ];

    /** Normaliza un encabezado: mayúsculas, sin acentos ni signos. */
    private function normalizarEncabezado(string $texto): string
    {
        $texto = mb_strtoupper(trim($texto));
        $texto = strtr($texto, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ñ' => 'N', 'Ü' => 'U', '°' => '',
        ]);
        $texto = preg_replace('/[^A-Z0-9]+/u', ' ', $texto);

        return trim(preg_replace('/\s+/', ' ', $texto));
    }

    /** Construye campo => índice de columna a partir de la fila de encabezados. */
    private function mapaColumnas(array $encabezado): array
    {
        $mapa = [];
        foreach ($encabezado as $idx => $titulo) {
            if ($titulo === null || $titulo === '') {
                continue;
            }
            $clave = $this->normalizarEncabezado((string) $titulo);
            if (isset(self::MAPA_EPI[$clave])) {
                $campo = self::MAPA_EPI[$clave];
                if (!isset($mapa[$campo])) {
                    $mapa[$campo] = $idx; // la primera coincidencia gana
                }
            }
        }

        return $mapa;
    }

    /**
     * Devuelve la fila como campo => valor. Si hay mapa de encabezados se usa ese
     * (los campos ausentes quedan en null); si no, se cae al orden por posición.
     */
    private function filaAsociativa(array $campos, array $mapa, array $posiciones): array
    {
        $out = [];
        foreach ($posiciones as $campo => $pos) {
            if (!empty($mapa)) {
                $out[$campo] = isset($mapa[$campo]) ? ($campos[$mapa[$campo]] ?? null) : null;
            } else {
                $out[$campo] = $campos[$pos] ?? null;
            }
        }

        return $out;
    }

    /**
     * Regla de validación por extensión (más fiable que `mimes` para xlsx,
     * cuyo mime real es application/zip).
     */
    private function reglaExtension(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $ext = strtolower($value->getClientOriginalExtension());
            if (!in_array($ext, self::EXTENSIONES, true)) {
                $fail('El archivo debe ser .' . implode(', .', self::EXTENSIONES));
            }
        };
    }

    public function importar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo_epidemiologia' => ['required', 'file', $this->reglaExtension()],
            'archivo_proa' => ['nullable', 'file', $this->reglaExtension()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Una importación completa procesa miles de filas y supera de largo el
        // max_execution_time por defecto de Apache (60 s).
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        DB::beginTransaction();

        try {
            // En un libro de Excel se busca la hoja por nombre; si no existe, se usa la primera.
            $filasEpidemiologia = $this->leerFilas($request->file('archivo_epidemiologia'), 'EPIDEMIOLOGIA');
            $resultadoEpidemiologia = $this->procesarEpidemiologia($filasEpidemiologia);

            $resultadoProa = ['procesados' => 0, 'actualizados' => 0, 'sinPaciente' => 0, 'omitidos' => 0, 'avisos' => []];
            if ($request->hasFile('archivo_proa')) {
                $filasProa = $this->leerFilas($request->file('archivo_proa'), 'PROA');
                $resultadoProa = $this->procesarProa($filasProa);
            }

            DB::commit();

            // Filas que se omitieron o recortaron: se informan sin bloquear la
            // importación, indicando de qué archivo viene cada una.
            $avisos = array_merge(
                array_map(fn ($a) => 'Epidemiología · ' . $a, $resultadoEpidemiologia['avisos'] ?? []),
                array_map(fn ($a) => 'PROA · ' . $a, $resultadoProa['avisos'] ?? [])
            );

            // Trazabilidad: registrar la importación
            $epiProc  = $resultadoEpidemiologia['procesados'] ?? 0;
            $proaProc = $resultadoProa['procesados'] ?? 0;
            Actividad::registrar([
                'tipo'        => 'importacion',
                'accion'      => 'importar',
                'descripcion' => "Importó archivos de seguimiento microbiológico (epidemiología: {$epiProc} registro(s)"
                                 . ($request->hasFile('archivo_proa') ? ", PROA: {$proaProc} registro(s)" : '') . ').',
            ]);

            $mensaje = 'Archivos procesados correctamente.';
            if ($avisos) {
                $mensaje = 'Archivos procesados con ' . count($avisos) . ' advertencia(s). Revisa el detalle abajo.';
            }
            $cursosNuevos = $resultadoProa['cursosNuevos'] ?? 0;
            if ($cursosNuevos > 0) {
                $mensaje .= ' Se detectaron ' . $cursosNuevos . ' curso(s) nuevo(s) de antibiótico (ver la campana de notificaciones).';
            }

            return redirect()->back()->with('success', $mensaje)
                ->with('resultado_epidemiologia', $resultadoEpidemiologia)
                ->with('resultado_proa', $resultadoProa)
                ->with('avisos_importacion', $avisos);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error importando seguimiento microbiologico: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'Error al procesar archivos: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Devuelve las filas del archivo como arreglos de celdas, sin importar si
     * viene en Excel (.xlsx/.ods), CSV o TXT.
     *
     * @param  string|null  $hojaPreferida  Nombre de hoja a buscar en libros de Excel
     */
    private function leerFilas($archivo, ?string $hojaPreferida = null): array
    {
        $ext = strtolower($archivo->getClientOriginalExtension());

        if (in_array($ext, ['xlsx', 'ods'], true)) {
            return $this->filasDesdeHojaCalculo($archivo->getRealPath(), $ext, $hojaPreferida);
        }

        return $this->filasDesdeTexto($this->leerArchivo($archivo));
    }

    private function leerArchivo($archivo): string
    {
        $contenido = file_get_contents($archivo->getRealPath());

        if (!mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252, ISO-8859-1, UTF-8');
        }

        return $contenido;
    }

    /**
     * Lee un libro de Excel/ODS. Si se indica una hoja preferida y existe, se usa
     * esa; si no, se usa la primera hoja con datos.
     */
    private function filasDesdeHojaCalculo(string $path, string $ext, ?string $hojaPreferida): array
    {
        $reader = $ext === 'ods'
            ? new \OpenSpout\Reader\ODS\Reader()
            : new \OpenSpout\Reader\XLSX\Reader();

        $reader->open($path);

        $filasPrimera = [];
        $filasPreferida = null;

        foreach ($reader->getSheetIterator() as $sheet) {
            $nombre = $this->normalizarTexto((string) $sheet->getName());
            // Coincide por nombre exacto o parcial: "CONSOLIDADO_PROA" también sirve para "PROA".
            $esPreferida = $hojaPreferida && str_contains($nombre, $this->normalizarTexto($hojaPreferida));

            if (!$esPreferida && !empty($filasPrimera)) {
                continue; // ya tenemos una primera hoja con datos
            }

            $filas = [];
            foreach ($sheet->getRowIterator() as $row) {
                $filas[] = array_map([$this, 'normalizarCelda'], $row->toArray());
            }

            if ($esPreferida) {
                $filasPreferida = $filas;
                break;
            }

            if (empty($filasPrimera)) {
                $filasPrimera = $filas;
            }
        }

        $reader->close();

        return $filasPreferida ?? $filasPrimera;
    }

    /** Errores de fórmula de Excel que deben tratarse como celda vacía. */
    private const ERRORES_EXCEL = ['#REF!', '#N/A', '#VALUE!', '#DIV/0!', '#NAME?', '#NULL!', '#NUM!'];

    /** Convierte una celda de Excel a texto normalizado (fechas y números incluidos). */
    private function normalizarCelda($valor)
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('H:i:s') === '00:00:00'
                ? $valor->format('Y-m-d')
                : $valor->format('Y-m-d H:i:s');
        }

        if (is_bool($valor)) {
            return $valor ? '1' : '0';
        }

        if (is_float($valor) && floor($valor) === $valor && abs($valor) < 1.0e+15) {
            return (string) (int) $valor; // 70.0 -> "70"
        }

        if (is_int($valor) || is_float($valor)) {
            return (string) $valor;
        }

        $valor = trim((string) $valor);

        // Los errores de fórmula (#REF!, #N/A, ...) no son datos.
        if (in_array(strtoupper($valor), self::ERRORES_EXCEL, true)) {
            return null;
        }

        return $valor === '' ? null : $valor;
    }

    /** Quita acentos y pasa a mayúsculas, para comparar nombres de hoja. */
    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtoupper(trim($texto));
        $texto = strtr($texto, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N']);

        return $texto;
    }

    /** Convierte texto plano (TXT/CSV) en filas, detectando el delimitador. */
    private function filasDesdeTexto(string $contenido): array
    {
        $lineas = $this->lineasValidas($contenido);
        if (empty($lineas)) {
            return [];
        }

        $delim = $this->detectarDelimitador($lineas[0]);

        $filas = [];
        foreach ($lineas as $linea) {
            $campos = in_array($delim, ["\t", '|'], true)
                ? explode($delim, $linea)
                : str_getcsv($linea, $delim);

            $filas[] = array_map(function ($v) {
                $v = trim((string) $v);

                return $v === '' ? null : $v;
            }, $campos);
        }

        return $filas;
    }

    /** El TXT de epidemiología viene con TAB; el de PROA con |; los CSV con ; o ,. */
    private function detectarDelimitador(string $linea): string
    {
        foreach (["\t", '|', ';'] as $d) {
            if (str_contains($linea, $d)) {
                return $d;
            }
        }

        return ',';
    }

    /** Rellena la fila hasta $ancho posiciones con null, para acceder por índice sin riesgo. */
    private function padFila(array $campos, int $ancho): array
    {
        return array_pad($campos, $ancho, null);
    }

    /** ¿La fila está completamente vacía? */
    private function filaVacia(array $campos): bool
    {
        foreach ($campos as $v) {
            if ($v !== null && $v !== '') {
                return false;
            }
        }

        return true;
    }

    private function procesarEpidemiologia(array $filas): array
    {
        $procesados = 0;
        $pacientesCreados = 0;
        $seguimientosCreados = 0;
        $omitidos = 0;
        $avisos = [];          // Filas descartadas, para informar al usuario.
        $cachePacientes = [];  // identificador_unico => Paciente, para no repetir consultas.

        // Si la primera fila es un encabezado, mapear las columnas por nombre.
        // Así el archivo puede traer las columnas en otro orden o cantidad.
        $mapa = [];
        $filas = array_values($filas);
        if (!empty($filas) && $this->esEncabezado($filas[0])) {
            $posible = $this->mapaColumnas($filas[0]);
            if (isset($posible['identificador_unico'])) {
                $mapa = $posible;
            }
        }

        foreach ($filas as $numeroFila => $campos) {
            $campos = $this->padFila($campos, 45);

            if ($this->filaVacia($campos) || $this->esEncabezado($campos)) {
                continue;
            }

            // Identificación del paciente para los avisos (se calcula antes del
            // try para que siga disponible si la fila falla más adelante).
            $f = $this->filaAsociativa($campos, $mapa, self::POSICIONES_EPI);
            $quien = $this->etiquetaPaciente(
                $this->limpiarNombre($f['nombre'] ?? null),
                $f['identificador_unico'] ?? null,
                $f['id_historia'] ?? null
            );

            // Una fila defectuosa no debe tumbar el resto de la importación.
            try {

            // ── Mapeo según estructura real del archivo de epidemiología ──
            //  0: Información del paciente   "NOMBRE COMPLETO (ID - dd/mm/aaaa)"
            //  1: ID del paciente            (cédula / identificador)
            //  2: Fecha de nacimiento
            //  3: Sexo                       (Masculino / Femenino)
            //  4: Código del paciente        (historia clínica)
            //  5: Tipo y zona                (tipo de muestra)
            //  6: N° de acceso               (n_reporte)
            //  7: ID del cliente de la muestra (sede / institución)
            //  8: Servicio                   (ubicación / sala)
            //  9: Fecha                      (fecha toma de muestra)
            // 10: Organismo                  (microorganismo)
            // 11: Cultivo                    (consecutivo de cultivo en la misma muestra)
            // 12: Fármacos sensibles
            // 13: Fármacos intermedios
            // 14: Fármacos resistentes
            // 15: Abreviaturas de los marcadores de resistencia
            $identificadorUnico = $this->limpiarValor($f['identificador_unico']);
            if (!$identificadorUnico) {
                $omitidos++;
                continue;
            }

            $datos = [
                'nombre'                 => $this->limpiarNombre($f['nombre']),
                'identificador_unico'    => $identificadorUnico,
                'fecha_nacimiento'       => $this->normalizarFecha($f['fecha_nacimiento']),
                'sexo'                   => $this->normalizarSexo($f['sexo']),
                'id_historia'            => $this->limpiarValor($f['id_historia']),
                // El tipo de muestra y el servicio llegan escritos de mil maneras:
                // se convierten al valor estandarizado si hay equivalencia, y si
                // no, entran tal cual y aparecen en Equivalencias > Pendientes.
                'tipo_muestra'           => Estandarizador::aplicar(
                                                Estandarizador::MUESTRA,
                                                $this->limpiarValor($f['tipo_muestra'])
                                            ),
                'n_reporte'              => $this->limpiarValor($f['n_reporte']),
                'sede'                   => $this->limpiarValor($f['sede']),
                'ubicacion'              => Estandarizador::aplicar(
                                                Estandarizador::SERVICIO,
                                                $this->limpiarValor($f['ubicacion'])
                                            ),
                'fecha_toma_muestra'     => $this->normalizarFecha($f['fecha_toma_muestra']),
                'microorganismo'         => $this->limpiarValor($f['microorganismo']),
                'cultivo_num'            => $this->limpiarValor($f['cultivo_num']),
                'sensibles'              => $this->limpiarValor($f['sensibles']),
                'intermedios'            => $this->limpiarValor($f['intermedios']),
                'resistentes'            => $this->limpiarValor($f['resistentes']),
                'marcadores_resistencia' => $this->limpiarValor($f['marcadores_resistencia']),
            ];

            // ── Datos complementarios (opcionales) ───────────────────────────
            // Solo se escriben los que vengan en el archivo; los ausentes quedan null.
            $comp = [
                'pais_origen'                => $this->limpiarValor($f['pais_origen']),
                'departamento'               => $this->limpiarValor($f['departamento']),
                'municipio'                  => $this->limpiarValor($f['municipio']),
                'diagnostico_ingreso'        => $this->limpiarValor($f['diagnostico_ingreso']),
                'asegurador'                 => $this->limpiarValor($f['asegurador']),
                'peso'                       => $this->limpiarValor($f['peso']),
                'fecha_ingreso_hosp'         => $this->normalizarFecha($f['fecha_ingreso_hosp']),
                'fecha_quirurgica_previa'    => $this->normalizarFecha($f['fecha_quirurgica_previa']),
                'categoria_quirurgica'       => $this->limpiarValor($f['categoria_quirurgica']),
                'egreso'                     => $this->limpiarValor($f['egreso']),
                'sitio'                      => Estandarizador::aplicar(
                                                    Estandarizador::SITIO,
                                                    $this->limpiarValor($f['sitio'])
                                                ),
                'tipo'                       => $this->limpiarValor($f['tipo']),
                'clasificacion'              => $this->limpiarValor($f['clasificacion']),
                'especialidad_cirugia'       => $this->limpiarValor($f['especialidad_cirugia']),
                'procedimiento_quirurgico'   => $this->limpiarValor($f['procedimiento_quirurgico']),
                'tiempo_quirurgico'          => $this->limpiarValor($f['tiempo_quirurgico']),
                'bano_quirurgico'            => $this->limpiarValor($f['bano_quirurgico']),
                'asepsia_quirurgica'         => $this->limpiarValor($f['asepsia_quirurgica']),
                'profilaxis'                 => $this->limpiarValor($f['profilaxis']),
                'antibioticos_usados'        => $this->limpiarValor($f['antibioticos_usados']),
                'asa_preoperatoria'          => $this->limpiarValor($f['asa_preoperatoria']),
                'tipo_cirugia'               => $this->limpiarValor($f['tipo_cirugia']),
                'clasificacion_cirugia'      => $this->limpiarValor($f['clasificacion_cirugia']),
                'puntaje_nnis'               => $this->limpiarValor($f['puntaje_nnis']),
                'revision_equipo'            => $this->limpiarValor($f['revision_equipo']),
                'interconsulta_infectologia' => $this->limpiarValor($f['interconsulta_infectologia']),
                'fecha_insercion'            => $this->normalizarFecha($f['fecha_insercion']),
                'fecha_retiro'               => $this->normalizarFecha($f['fecha_retiro']),
                'comentarios'                => $this->limpiarValor($f['comentarios']),
            ];
            // Derivados (igual que en la página)
            $comp['clasificacion_texto'] = $this->calcularClasificacionTexto($comp['tipo'], $comp['clasificacion'], $comp['sitio']);
            $comp['dias_entre_qx_e_infeccion'] = $this->calcularDiasQx($comp['fecha_quirurgica_previa'], $datos['fecha_toma_muestra']);
            // Solo agregar los que traen valor (no pisar con null si no vienen)
            $datos = array_merge($datos, array_filter($comp, fn ($v) => !is_null($v)));

            // Un mismo paciente aparece en muchas filas del archivo. Se resuelve
            // una sola vez por importación para no repetir SELECT + UPDATE.
            $clavePaciente = $datos['identificador_unico'];

            if (isset($cachePacientes[$clavePaciente])) {
                $paciente = $cachePacientes[$clavePaciente];
            } else {
                $paciente = Paciente::updateOrCreate(
                    ['identificador_unico' => $datos['identificador_unico']],
                    [
                        'nombre'           => $datos['nombre'],
                        'id_historia'      => $datos['id_historia'],
                        'fecha_nacimiento' => $datos['fecha_nacimiento'],
                        'sexo'             => $datos['sexo'],
                    ]
                );

                if ($paciente->wasRecentlyCreated) {
                    $pacientesCreados++;
                }

                $cachePacientes[$clavePaciente] = $paciente;
            }

            // Clave única lógica: identificador + n_reporte + microorganismo + cultivo_num
            $registro = EpidemiologiaRegistro::firstOrNew([
                'identificador_unico' => $datos['identificador_unico'],
                'n_reporte'           => $datos['n_reporte'],
                'microorganismo'      => $datos['microorganismo'],
                'cultivo_num'         => $datos['cultivo_num'],
            ]);

            $payload = array_merge($datos, ['paciente_id' => $paciente->id]);

            // Integridad de datos (req. 1): CULTIVO / SENSIBLES / INTERMEDIOS /
            // RESISTENTES / MARCADORES se usan en PROA y NO se pueden perder.
            // Si el registro ya tiene valor y la fila entrante trae el campo vacío,
            // se conserva el valor existente (no se pisa con vacío).
            if ($registro->exists) {
                foreach (['cultivo_num', 'sensibles', 'intermedios', 'resistentes', 'marcadores_resistencia'] as $campo) {
                    $nuevo = $payload[$campo] ?? null;
                    if (($nuevo === null || $nuevo === '') && filled($registro->{$campo})) {
                        unset($payload[$campo]);
                    }
                }
            }

            $registro->fill($payload)->save();

            if ($registro->wasRecentlyCreated) {
                $seguimientosCreados++;
            }

            $procesados++;

            } catch (\Throwable $e) {
                $omitidos++;
                // Si el archivo trae encabezados se usa el mapa real; si no, las posiciones fijas.
                $avisos[] = 'Fila ' . ($numeroFila + 1) . ' — ' . $quien . ': '
                          . $this->motivoLegible($e, $mapa ?: self::POSICIONES_EPI);
                Log::warning('Epidemiología fila ' . ($numeroFila + 1) . ' (' . $quien . ') omitida: ' . $e->getMessage());
            }
        }

        return compact('procesados', 'pacientesCreados', 'seguimientosCreados', 'omitidos', 'avisos');
    }

    private function procesarProa(array $filas): array
    {
        $procesados = 0;
        $actualizados = 0;
        $sinPaciente = 0;
        $omitidos = 0;
        $avisos = [];          // Filas recortadas o descartadas, para informar al usuario.
        $cachePacientes = [];  // documento|historia => Paciente|null
        $cacheRegistros = [];  // paciente_id => EpidemiologiaRegistro
        $nuevas = [];          // dosis creadas en esta importación (para detectar cursos nuevos)

        // Encabezado para agrupar los detalles de esta importación.
        $encabezado = EncabezadoProcedimiento::create([
            'fecha_procedimiento' => now(),
            'Nom_procedimiento'   => 'Importación PROA ' . now()->format('Y-m-d H:i:s'),
        ]);

        $boolSi = function ($v) {
            if ($v === null) {
                return null;
            }
            return in_array(mb_strtolower(trim((string) $v)), ['si', 'sí', '1', 'true', 'x', 'yes'], true);
        };

        // La fecha que se muestra en el bloque (Fec_Sumistro) sale de la columna
        // "Fecha inicio antibiótico" del Excel (columna W). La columna que el
        // formato esperaba ("Fecha hora suministro", T) viene vacía. Se localiza
        // por NOMBRE en el encabezado para no depender de la posición; si no
        // aparece, se usa la columna W (índice 22).
        $filas = array_values($filas);
        $idxFechaInicio = null;
        if (!empty($filas)) {
            foreach ((array) $filas[0] as $i => $titulo) {
                if (str_contains($this->normalizarTexto((string) $titulo), 'FECHA INICIO ANTIBIOTICO')) {
                    $idxFechaInicio = (int) $i;
                    break;
                }
            }
        }
        if ($idxFechaInicio === null) {
            $idxFechaInicio = 22; // columna W (0-indexada)
        }

        foreach ($filas as $numeroFila => $campos) {
            $campos = $this->padFila($campos, max(48, $idxFechaInicio + 1));

            if ($this->filaVacia($campos) || $this->esEncabezado($campos)) {
                continue;
            }

            // Identificación del paciente para los avisos: nombre (10,11,12),
            // documento (9) e historia clínica (7).
            $quien = $this->etiquetaPaciente(
                implode(' ', array_filter([$campos[10] ?? null, $campos[11] ?? null, $campos[12] ?? null])),
                $campos[9] ?? null,
                $campos[7] ?? null
            );

            // Una fila defectuosa no debe tumbar el resto de la importación:
            // se registra el motivo, se omite y se continúa.
            try {

            // Sin documento ni historia no se puede identificar al paciente.
            if (empty($campos[9]) && empty($campos[7])) {
                continue;
            }

            // Las fechas pueden venir como texto dd/mm/aaaa. Se normalizan antes de
            // insertar, porque el modelo castea F_Ingreso/Fec_Sumistro a fecha.
            $campos[4]  = $this->normalizarFechaHora($campos[4] ?? null);   // F_Ingreso
            $campos[14] = $this->normalizarFecha($campos[14] ?? null);      // Fec_Nacimiento
            // Fec_Sumistro toma su valor de la columna "Fecha inicio antibiótico".
            $fechaInicioRaw = $campos[$idxFechaInicio] ?? null;
            if (!empty($fechaInicioRaw)) {
                $campos[19] = $fechaInicioRaw;
            }
            $campos[19] = $this->normalizarFechaHora($campos[19] ?? null);  // Fecha de inicio del antibiótico

            // 1. Crear SIEMPRE la fila en deta_procedimientos (esto hace aparecer
            //    el bloque PROA y permite el cruce por documento / historia).
            $recortados = [];
            $procCreado = Procedimiento::insertarDesdeCampos($campos, $encabezado->id_procedimiento, $recortados);
            // Guardar para detectar cursos nuevos de antibiótico al finalizar.
            if ($procCreado && !empty($campos[9])) {
                $nuevas[] = [
                    'id'          => $procCreado->id,
                    'ident'       => (string) $campos[9],
                    'antibiotico' => (string) $procCreado->Antimicrobiano,
                ];
            }
            $procesados++;

            if ($recortados) {
                foreach ($recortados as $campo => $largo) {
                    $avisos[] = 'Fila ' . ($numeroFila + 1) . ' — ' . $quien
                              . ": el campo «{$campo}»" . $this->referenciaColumna($campo, self::ORIGEN_PROA)
                              . " traía {$largo} caracteres y se recortó.";
                }
            }

            // 2. Reflejar procedimiento + intervención en el seguimiento del paciente.
            $identificador = $campos[9] ?? null;
            $historia = $campos[7] ?? null;

            // Un paciente aparece en muchas filas (una por dosis). Se resuelve
            // una sola vez por importación.
            $clavePaciente = $identificador . '|' . $historia;

            if (array_key_exists($clavePaciente, $cachePacientes)) {
                $paciente = $cachePacientes[$clavePaciente];
            } else {
                $paciente = Paciente::where('identificador_unico', $identificador)
                    ->orWhere('id_historia', $historia)
                    ->first();
                $cachePacientes[$clavePaciente] = $paciente;
            }

            if (!$paciente) {
                $sinPaciente++;
                continue;
            }

            $instrucciones = $campos[18] ?? null;
            [$cantidad, $viaAplicacion, $tiempoHoras, $diasAntibiotico] = $this->parsearInstruccionesMedicamento($instrucciones);
            [$fechaSuministro] = $this->parsearFechaHora($campos[19] ?? null);

            // El seguimiento destino es siempre el mismo para un paciente dado,
            // así que también se resuelve una sola vez.
            if (isset($cacheRegistros[$paciente->id])) {
                $registro = $cacheRegistros[$paciente->id];
            } else {
                $registro = EpidemiologiaRegistro::where('paciente_id', $paciente->id)
                    ->orderByDesc('fecha_toma_muestra')
                    ->first();

                if (!$registro) {
                    $registro = EpidemiologiaRegistro::create([
                        'paciente_id' => $paciente->id,
                        'nombre' => $paciente->nombre,
                        'id_historia' => $paciente->id_historia,
                        'fecha_nacimiento' => $paciente->fecha_nacimiento,
                        'sexo' => $paciente->sexo,
                        'identificador_unico' => $paciente->identificador_unico,
                    ]);
                }

                $cacheRegistros[$paciente->id] = $registro;
            }

            // Sección 4 — Procedimiento (columnas 0..19)
            $procedimiento = [
                'cod_episodio' => $campos[0] ?? null,
                'nom_sala' => $campos[2] ?? null,
                'num_cama' => $campos[3] ?? null,
                'fecha_ingreso' => $this->normalizarFechaHora($campos[4] ?? null),
                'nombre_eps' => $campos[6] ?? null,
                'edad' => $this->calcularEdad($campos[14] ?? null),
                'cod_diag' => $campos[15] ?? null,
                'cie10' => $campos[15] ?? null,
                'diagnostico' => $campos[16] ?? null,
                'antimicrobiano' => Procedimiento::extractNombreMedicamento($campos[17] ?? null),
                'cantidad' => $cantidad,
                'presentacion' => $campos[17] ?? null,
                'via_aplicacion' => $viaAplicacion,
                'frecuencia_suministro' => $tiempoHoras,
                'dias_antibiotico' => $diasAntibiotico,
                'fecha_suministro' => $fechaSuministro,
            ];

            // Sección 5 — Intervención PROA (columnas 20..47, opcionales)
            $intervencion = [
                'mes'                      => $this->limpiarValor($campos[20] ?? null),
                'fecha_intervencion'       => $this->normalizarFecha($campos[21] ?? null),
                'fecha_inicio_antibiotico' => $this->normalizarFecha($campos[$idxFechaInicio] ?? null),
                'dosis_suministrada'       => $this->limpiarValor($campos[23] ?? null),
                'sistema_internacional'    => $this->limpiarValor($campos[24] ?? null),
                'perfil_antimicrobiano'    => $this->limpiarValor($campos[25] ?? null),
                'especialista_tratante'    => $this->limpiarValor($campos[26] ?? null),
                'diagnostico_infeccioso'   => $this->limpiarValor($campos[27] ?? null),
                'dosis_adecuada'           => $this->normalizarSiNo($campos[28] ?? null),
                'fecha_fin_antibiotico'    => $this->normalizarFecha($campos[29] ?? null),
                'tiempo_tratamiento'       => $this->limpiarValor($campos[30] ?? null),
                'duracion_adecuada'        => $this->normalizarSiNo($campos[31] ?? null),
                'cultivo_previo'           => $boolSi($campos[32] ?? null),
                'resultado_cultivo'        => $this->limpiarValor($campos[33] ?? null),
                'solicitudes_pruebas'      => $this->limpiarValor($campos[34] ?? null),
                'oportunidad_reporte'      => $this->limpiarValor($campos[35] ?? null),
                'indicacion_terapia'       => $this->limpiarValor($campos[36] ?? null),
                'tratamiento'              => $this->limpiarValor($campos[37] ?? null),
                'valoracion_grupo1'        => $this->normalizarSiNo($campos[38] ?? null),
                'valoracion_uci'           => $this->normalizarSiNo($campos[39] ?? null),
                'fecha_valoracion'         => $this->normalizarFecha($campos[40] ?? null),
                'ajuste_prescripcion'      => $this->normalizarSiNo($campos[41] ?? null),
                'adherencia_proa'          => $this->normalizarSiNo($campos[42] ?? null),
                'adherencia_guias'         => $this->normalizarSiNo($campos[43] ?? null),
                'razon_no_adherencia'      => $this->limpiarValor($campos[44] ?? null),
                'observacion'              => $this->limpiarValor($campos[45] ?? null),
                'caso_cerrado'             => $boolSi($campos[46] ?? null),
                'mortalidad'               => $boolSi($campos[47] ?? null),
            ];
            $intervencion = array_filter($intervencion, fn ($v) => !is_null($v));
            $hayIntervencion = !empty($intervencion);

            $registro->update(array_merge(
                $procedimiento,
                $intervencion,
                ['tiene_procedimiento' => true, 'tiene_intervencion_proa' => $hayIntervencion]
            ));

            $actualizados++;

            } catch (\Throwable $e) {
                $omitidos++;
                $avisos[] = 'Fila ' . ($numeroFila + 1) . ' — ' . $quien . ': ' . $this->motivoLegible($e, self::ORIGEN_PROA);
                Log::warning('PROA fila ' . ($numeroFila + 1) . ' (' . $quien . ') omitida: ' . $e->getMessage());
            }
        }

        // Detectar cursos NUEVOS de antibiótico (re-tratamientos a más de 7 días)
        // y avisar en la bandeja de notificaciones.
        $cursosNuevos = $this->detectarCursosNuevos($nuevas);

        return compact('procesados', 'actualizados', 'sinPaciente', 'omitidos', 'avisos', 'cursosNuevos');
    }

    /**
     * A partir de las dosis creadas en esta importación, detecta cursos nuevos
     * de antibiótico (segundo curso o posterior del mismo fármaco en el mismo
     * paciente, a 7+ días del anterior) y crea una notificación por cada uno.
     */
    private function detectarCursosNuevos(array $nuevas): int
    {
        if (!$nuevas) {
            return 0;
        }

        $creadas = 0;

        // Agrupar las dosis nuevas por (documento, antibiótico).
        $porGrupo = collect($nuevas)->groupBy(fn ($n) => $n['ident'] . '||' . $n['antibiotico']);

        foreach ($porGrupo as $clave => $items) {
            [$ident, $antibiotico] = array_pad(explode('||', $clave, 2), 2, '');
            if ($ident === '' || $antibiotico === '') {
                continue;
            }

            $idsNuevos = collect($items)->pluck('id')->all();

            // Todas las dosis de ese antibiótico para ese paciente.
            $dosis = Procedimiento::where('Num_Ident', $ident)
                ->where('Antimicrobiano', $antibiotico)
                ->get(['id', 'Num_Ident', 'Antimicrobiano', 'Fec_Sumistro', 'Nom_Sala']);

            if ($dosis->count() < 2) {
                continue; // sin re-tratamiento posible
            }

            $cursos = \App\Support\ProaCursos::agrupar($dosis);

            foreach ($cursos as $i => $curso) {
                if ($i === 0) {
                    continue; // el primer curso no es re-tratamiento
                }
                $rep = $curso['representativa'] ?? null;
                // Solo si el curso lo abrió una dosis de ESTA importación.
                if (!$rep || !in_array($rep->id, $idsNuevos, true)) {
                    continue;
                }
                // Evita inundar con re-tratamientos antiguos del histórico:
                // solo se notifican cursos iniciados en los últimos 60 días.
                if ($curso['inicio'] && $curso['inicio']->lt(\Carbon\Carbon::today()->subDays(60))) {
                    continue;
                }

                $fecha = $curso['inicio']?->format('d/m/Y') ?? 'sin fecha';

                $notif = \App\Models\Notificacion::crear([
                    'tipo'             => 'proa',
                    'titulo'           => 'Nuevo tratamiento: ' . $antibiotico,
                    'mensaje'          => 'Se detectó un nuevo curso de ' . $antibiotico
                                          . ' (paciente doc. ' . $ident . ') iniciado el ' . $fecha
                                          . ', a más de 7 días del curso anterior del mismo antibiótico.',
                    'url'              => \App\Models\Notificacion::urlRegistroPaciente($ident),
                    'referencia_tabla' => 'deta_procedimientos',
                    'referencia_id'    => $rep->id,
                ]);

                if ($notif) {
                    $creadas++;
                }
            }
        }

        return $creadas;
    }

    /**
     * Columna de origen (índice 0) de cada campo de deta_procedimientos.
     * Permite decirle a quien importa qué columna del archivo revisar.
     */
    private const ORIGEN_PROA = [
        'Cod_Episodio'      => 0,
        'Cod_Sala'          => 1,
        'Nom_Sala'          => 2,
        'Num_Cama'          => 3,
        'F_Ingreso'         => 4,
        'Cod_Eps'           => 5,
        'Nom_Eps'           => 6,
        'Hist_Clinica'      => 7,
        'Tipo_Ident'        => 8,
        'Num_Ident'         => 9,
        'Medico_Trata'      => 10,
        'Cod_Diag'          => 15,
        'CIE10'             => 15,
        'Diagnostico'       => 16,
        'Antimicrobiano'    => 17,
        'Presentacion'      => 17,
        'Cantidad'          => 18,
        'Via_Aplicacion'    => 18,
        'Tiem_Horas'        => 18,
        'Dias_Antibioticos' => 18,

        // Los mismos datos reflejados en seguimiento_microbiologico (sección 4)
        'cod_episodio'      => 0,
        'nom_sala'          => 2,
        'num_cama'          => 3,
        'fecha_ingreso'     => 4,
        'nombre_eps'        => 6,
        'cod_diag'          => 15,
        'cie10'             => 15,
        'diagnostico'       => 16,
        'antimicrobiano'    => 17,
        'presentacion'      => 17,

        // Intervención PROA (sección 5) — aquí viven los ENUM que suelen fallar
        'mes'                      => 20,
        'fecha_intervencion'       => 21,
        'fecha_inicio_antibiotico' => 22,
        'dosis_suministrada'       => 23,
        'sistema_internacional'    => 24,
        'perfil_antimicrobiano'    => 25,
        'especialista_tratante'    => 26,
        'diagnostico_infeccioso'   => 27,
        'dosis_adecuada'           => 28,
        'fecha_fin_antibiotico'    => 29,
        'tiempo_tratamiento'       => 30,
        'duracion_adecuada'        => 31,
        'cultivo_previo'           => 32,
        'resultado_cultivo'        => 33,
        'solicitudes_pruebas'      => 34,
        'oportunidad_reporte'      => 35,
        'indicacion_terapia'       => 36,
        'tratamiento'              => 37,
        'valoracion_grupo1'        => 38,
        'valoracion_uci'           => 39,
        'fecha_valoracion'         => 40,
        'ajuste_prescripcion'      => 41,
        'adherencia_proa'          => 42,
        'adherencia_guias'         => 43,
        'razon_no_adherencia'      => 44,
        'observacion'              => 45,
        'caso_cerrado'             => 46,
        'mortalidad'               => 47,
    ];

    /**
     * Convierte un índice de columna (0 = A) en su letra de Excel.
     */
    private function columnaExcel(int $indice): string
    {
        $letra = '';
        for ($n = $indice; $n >= 0; $n = intdiv($n, 26) - 1) {
            $letra = chr(65 + ($n % 26)) . $letra;
        }

        return $letra;
    }

    /**
     * Referencia legible a la columna del archivo de origen.
     * Devuelve cadena vacía si el campo no tiene una columna conocida.
     */
    private function referenciaColumna(string $campo, array $origen): string
    {
        if (!array_key_exists($campo, $origen) || !is_int($origen[$campo])) {
            return '';
        }

        return ' — columna ' . $this->columnaExcel($origen[$campo]) . ' del archivo';
    }

    /**
     * Etiqueta legible del paciente, para poder ubicar la fila en el archivo
     * de origen sin depender solo del número de línea.
     */
    private function etiquetaPaciente($nombre, $documento, $historia): string
    {
        $nombre    = trim((string) $nombre);
        $documento = trim((string) $documento);
        $historia  = trim((string) $historia);

        $identificadores = [];
        if ($documento !== '') {
            $identificadores[] = 'ID ' . $documento;
        }
        if ($historia !== '') {
            $identificadores[] = 'HC ' . $historia;
        }

        $partes = [];
        if ($nombre !== '') {
            $partes[] = $nombre;
        }
        if ($identificadores) {
            $partes[] = '(' . implode(' · ', $identificadores) . ')';
        }

        return $partes ? implode(' ', $partes) : 'paciente sin identificar';
    }

    /**
     * Traduce el error crudo de la base a algo accionable para quien importa.
     */
    private function motivoLegible(\Throwable $e, array $origen = []): string
    {
        $mensaje = $e->getMessage();

        if (preg_match("/Data too long for column '([^']+)'/", $mensaje, $m)) {
            return "el campo «{$m[1]}»" . $this->referenciaColumna($m[1], $origen)
                 . ' trae un valor más largo del permitido.';
        }

        if (preg_match("/Data truncated for column '([^']+)'/", $mensaje, $m)) {
            return "el campo «{$m[1]}»" . $this->referenciaColumna($m[1], $origen)
                 . ' trae un valor que no está entre las opciones válidas.';
        }

        if (preg_match("/Incorrect (date|datetime|integer) value: '([^']*)' for column (.+?) at row/", $mensaje, $m)) {
            // MySQL puede reportar `base`.`tabla`.`columna`; interesa el último tramo.
            $tramos = array_values(array_filter(preg_split('/[`\'.]+/', $m[3])));
            $campo  = $tramos ? end($tramos) : $m[3];

            return "el campo «{$campo}»" . $this->referenciaColumna($campo, $origen)
                 . " trae «{$m[2]}», que no es un valor de tipo {$m[1]} válido.";
        }

        if (preg_match("/Incorrect (date|datetime|integer) value: '([^']*)'/", $mensaje, $m)) {
            return "valor de tipo {$m[1]} inválido: «{$m[2]}».";
        }

        return \Illuminate\Support\Str::limit($mensaje, 160);
    }

    private function lineasValidas(string $contenido): array
    {
        return array_values(array_filter(preg_split('/\r?\n/', $contenido), fn($linea) => trim($linea) !== ''));
    }

    private function esEncabezado(array $campos): bool
    {
        $primero = mb_strtoupper((string) ($campos[0] ?? ''));
        return str_contains($primero, 'INFORMACI')         // "Información del paciente"
            || str_contains($primero, 'NOMBRE')
            || str_contains($primero, 'COD_EPISODIO');
    }

    private function limpiarValor(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }
        $valor = trim($valor);
        return $valor === '' ? null : $valor;
    }

    /**
     * Normaliza valores de campos ENUM Si/No/No aplica provenientes del Excel.
     * Variantes como "NO APLICA", "N/A", "SI" se mapean al valor canónico.
     * Valores no reconocibles devuelven null para evitar truncamiento.
     */
    private function normalizarSiNo(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }
        $v = mb_strtoupper(trim($valor));

        if (in_array($v, ['SI', 'SÍ', 'S', 'YES', '1', 'TRUE'])) {
            return 'Si';
        }
        if (in_array($v, ['NO', 'N', 'NO.', '0', 'FALSE'])) {
            return 'No';
        }
        if (in_array($v, ['NO APLICA', 'NO APLICA.', 'NO APLICABLE', 'N/A', 'NA', 'N.A.', 'N.A', 'NO APLICA.'])) {
            return 'No aplica';
        }
        if (in_array($v, ['PARCIAL', 'PARCIALMENTE', 'P'])) {
            return 'Parcial';
        }

        // Valor no reconocido: devolver null para no generar truncamiento
        return null;
    }

    private function limpiarNombre(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }
        // "ANGULO REINA LUIS ANGEL (16474122 - 26/11/1955)" → "ANGULO REINA LUIS ANGEL"
        $nombre = preg_replace('/\s*\([^)]*\)\s*$/u', '', $valor);
        $nombre = trim((string) $nombre);
        return $nombre === '' ? null : $nombre;
    }

    private function normalizarSexo(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }
        $v = mb_strtoupper(trim($valor));
        if (str_starts_with($v, 'M')) {
            return 'M';
        }
        if (str_starts_with($v, 'F')) {
            return 'F';
        }
        return $v;
    }

    private function normalizarFecha(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        try {
            return (new \DateTime(str_replace('/', '-', $valor)))->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizarFechaHora(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        try {
            return (new \DateTime(str_replace('/', '-', $valor)))->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parsearFechaHora(?string $valor): array
    {
        $fecha = $this->normalizarFechaHora($valor);
        return [$fecha ? substr($fecha, 0, 10) : null, $fecha ? substr($fecha, 11) : null];
    }

    private function parsearInstruccionesMedicamento(?string $valor): array
    {
        if (!$valor) {
            return [null, null, null, null];
        }

        $partes = array_map('trim', explode(',', $valor));
        return [$partes[0] ?? null, $partes[1] ?? null, $partes[2] ?? null, $partes[3] ?? null];
    }

    private function calcularEdad(?string $fechaNacimiento): ?int
    {
        if (!$fechaNacimiento) {
            return null;
        }

        try {
            return (new \DateTime())->diff(new \DateTime(str_replace('/', '-', $fechaNacimiento)))->y;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Clasificación en texto a partir de tipo + clasificación (+ sitio).
     * Misma lógica que EpidemiologiaController y la vista.
     */
    private function calcularClasificacionTexto(?string $tipo, ?string $clasificacion, ?string $sitio): ?string
    {
        if (!$tipo || !$clasificacion) {
            return null;
        }

        if ($tipo == '1') {
            return match ((string) $clasificacion) {
                '1' => 'Colonización Intrahospitalaria',
                '2' => 'Contaminado Intrahospitalaria',
                '3' => 'Infección',
                '4' => $sitio === '51. Infeccion Previa' ? 'Infección Previa'
                     : ($sitio === '52. No cumple criterios' ? 'No Cumple Criterios' : 'No aplica'),
                '5' => 'Infección sin Mios',
                '6' => 'Infección Polimicrobiano',
                '7' => 'Complicación',
                default => 'No aplica',
            };
        }
        if ($tipo == '2') {
            return match ((string) $clasificacion) {
                '1' => 'Colonización Extrahospitalaria',
                '2' => 'Contaminado Extrahospitalaria',
                '3' => 'Infección Extrahospitalaria',
                default => 'No aplica',
            };
        }
        return 'No aplica';
    }

    /**
     * Días entre la cirugía previa y la toma de muestra (puede ser negativo).
     * Recibe fechas ya normalizadas (Y-m-d).
     */
    private function calcularDiasQx(?string $fechaQx, ?string $fechaMuestra): ?int
    {
        if (!$fechaQx || !$fechaMuestra) {
            return null;
        }
        try {
            // Fórmula: (fecha toma de muestra) - (fecha quirúrgica previa), en días.
            $m = (new \DateTime($fechaMuestra))->setTime(0, 0, 0);
            $q = (new \DateTime($fechaQx))->setTime(0, 0, 0);
            return (int) round(($m->getTimestamp() - $q->getTimestamp()) / 86400);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
