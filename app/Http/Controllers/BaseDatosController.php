<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo "Base de datos": diccionario de datos legible de las tablas más
 * importantes. Los nombres de campo se leen EN VIVO de la base para que
 * siempre coincidan con la estructura real; las descripciones son curadas.
 */
class BaseDatosController extends Controller
{
    public function index()
    {
        // Orden y presentación de las tablas importantes.
        $definicion = [
            [
                'tabla'       => 'pacientes',
                'titulo'      => 'Pacientes',
                'icono'       => 'fa-user-injured',
                'color'       => '#2a377e',
                'descripcion' => 'Maestro de pacientes. Una fila por persona; los demás módulos se relacionan con ella.',
            ],
            [
                'tabla'       => 'seguimiento_microbiologico',
                'titulo'      => 'Seguimiento microbiológico (Epidemiología)',
                'icono'       => 'fa-vial',
                'color'       => '#2a377e',
                'descripcion' => 'Muestras y cultivos. Una fila por muestra/aislamiento, con su antibiograma y los datos complementarios del caso.',
            ],
            [
                'tabla'       => 'casos_microorganismo',
                'titulo'      => 'Casos de microorganismo',
                'icono'       => 'fa-layer-group',
                'color'       => '#5a4b8a',
                'descripcion' => 'Agrupa las muestras del mismo germen de un paciente en un caso. Guarda los datos complementarios comunes.',
            ],
            [
                'tabla'       => 'deta_procedimientos',
                'titulo'      => 'Detalle de procedimientos (Dosis PROA)',
                'icono'       => 'fa-capsules',
                'color'       => '#2e7d5b',
                'descripcion' => 'Suministros de antimicrobianos importados. Una fila por dosis.',
            ],
            [
                'tabla'       => 'intervenciones_proa',
                'titulo'      => 'Intervenciones PROA',
                'icono'       => 'fa-clipboard-check',
                'color'       => '#2e7d5b',
                'descripcion' => 'La intervención que el equipo PROA registra sobre una dosis. Una fila por dosis intervenida.',
            ],
            [
                'tabla'       => 'actividades',
                'titulo'      => 'Actividades (Auditoría)',
                'icono'       => 'fa-history',
                'color'       => '#8a6d3b',
                'descripcion' => 'Bitácora de trazabilidad: quién hizo qué y cuándo.',
            ],
        ];

        $desc = $this->descripciones();

        $tablas = [];
        foreach ($definicion as $def) {
            if (!Schema::hasTable($def['tabla'])) {
                continue;
            }

            $columnas = [];
            foreach (DB::select('SHOW COLUMNS FROM `' . $def['tabla'] . '`') as $c) {
                $columnas[] = [
                    'campo'       => $c->Field,
                    'tipo'        => $this->tipoLegible($c->Type),
                    'descripcion' => $desc[$def['tabla']][$c->Field] ?? '—',
                ];
            }

            $def['columnas']    = $columnas;
            $def['total_campos'] = count($columnas);
            $tablas[] = $def;
        }

        return view('base_datos.index', compact('tablas'));
    }

    /**
     * Convierte el tipo SQL en una etiqueta legible en español.
     */
    private function tipoLegible(string $tipo): string
    {
        $t = strtolower($tipo);

        if (str_starts_with($t, 'tinyint(1)'))                return 'Sí / No';
        if (str_starts_with($t, 'int') || str_starts_with($t, 'bigint')
            || str_starts_with($t, 'smallint') || str_starts_with($t, 'decimal')
            || str_starts_with($t, 'float') || str_starts_with($t, 'double')) return 'Número';
        if (str_starts_with($t, 'date') && !str_contains($t, 'datetime')) return 'Fecha';
        if (str_contains($t, 'datetime') || str_contains($t, 'timestamp')) return 'Fecha y hora';
        if (str_starts_with($t, 'time'))     return 'Hora';
        if (str_starts_with($t, 'enum'))     return 'Opciones';
        if (str_starts_with($t, 'text') || str_starts_with($t, 'longtext') || str_starts_with($t, 'mediumtext')) return 'Texto largo';
        if (str_starts_with($t, 'char') || str_starts_with($t, 'varchar')) return 'Texto';

        return $tipo;
    }

    /**
     * Diccionario curado: [tabla][campo] => descripción (con la etiqueta del
     * formulario cuando aplica).
     */
    private function descripciones(): array
    {
        $timestamps = [
            'created_at' => 'Fecha en que se creó el registro (automático).',
            'updated_at' => 'Fecha de la última modificación (automático).',
        ];

        return [
            'pacientes' => [
                'id'                  => 'Identificador interno del paciente (llave primaria).',
                'nombre'              => 'Nombre completo del paciente.',
                'id_historia'         => 'Número de historia clínica (HC).',
                'fecha_nacimiento'    => 'Fecha de nacimiento.',
                'sexo'                => 'Sexo del paciente (M / F).',
                'identificador_unico' => 'Número de documento de identidad (cédula, TI, CE…).',
                'tipo_identificacion' => 'Tipo de documento (CC, TI, CE, RC…).',
            ] + $timestamps,

            'seguimiento_microbiologico' => [
                'id'                    => 'Identificador interno de la muestra (llave primaria).',
                'paciente_id'           => 'Enlace al paciente dueño de la muestra (tabla pacientes).',
                'caso_id'               => 'Enlace al caso de microorganismo que agrupa esta muestra.',
                'tipo_id'               => 'Tipo de documento del paciente (etiqueta «Tipo ID»).',
                'nombre'                => 'Nombre del paciente (copiado para consulta rápida).',
                'id_historia'           => 'Historia clínica (etiqueta «Historia Clínica»).',
                'fecha_nacimiento'      => 'Fecha de nacimiento del paciente.',
                'sexo'                  => 'Sexo del paciente (etiqueta «Sexo»).',
                'identificador_unico'   => 'Documento del paciente (etiqueta «ID»).',
                // ── Datos de la muestra ──
                'tipo_muestra'          => 'Tipo de muestra tomada (etiqueta «Tipo de Muestra»).',
                'n_reporte'             => 'Número de reporte del laboratorio (etiqueta «N. Reporte»).',
                'sede'                  => 'Sede donde se procesó (etiqueta «Sede»).',
                'ubicacion'             => 'Servicio/ubicación del paciente (etiqueta «Ubicación»); define los bloques de servicio.',
                'fecha_toma_muestra'    => 'Fecha en que se tomó la muestra (etiqueta «Fecha Toma de Muestra»).',
                'microorganismo'        => 'Germen aislado (etiqueta «Microorganismo»).',
                'cultivo_num'           => 'Consecutivo del cultivo en la misma muestra (etiqueta «Cultivo»).',
                'sensibles'             => 'Antibióticos a los que el germen es sensible (etiqueta «Sensibles»).',
                'intermedios'           => 'Antibióticos de sensibilidad intermedia (etiqueta «Intermedios»).',
                'resistentes'           => 'Antibióticos a los que es resistente (etiqueta «Resistentes»).',
                'marcadores_resistencia' => 'Marcadores/genes de resistencia (etiqueta «Marcadores»).',
                // ── Banderas ──
                'tiene_procedimiento'   => 'Indica si el paciente tiene antimicrobiano registrado (PROA).',
                'tiene_intervencion_proa' => 'Indica si tiene una intervención PROA registrada.',
                'caso_cerrado'          => 'Indica si el caso PROA está cerrado (etiqueta «Caso Cerrado»).',
                'mortalidad'            => 'Indica mortalidad (etiqueta «Mortalidad»).',
                // ── Datos reflejados de PROA ──
                'cod_episodio'          => 'Código del episodio de atención (de PROA).',
                'nom_sala'              => 'Sala (etiqueta «Sala»).',
                'num_cama'              => 'Cama (etiqueta «Cama»).',
                'fecha_ingreso'         => 'Fecha de ingreso hospitalario (etiqueta «Fecha de Ingreso»).',
                'nombre_eps'            => 'Asegurador/EPS (etiqueta «EPS»).',
                'edad'                  => 'Edad del paciente.',
                'medico_tratante'       => 'Médico tratante / nombre.',
                'cod_diag'              => 'Código de diagnóstico.',
                'cie10'                 => 'Código CIE-10 del diagnóstico (etiqueta «CIE-10»).',
                'diagnostico'           => 'Diagnóstico (etiqueta «Diagnósticos»).',
                'antimicrobiano'        => 'Antimicrobiano suministrado (etiqueta «Antimicrobianos»).',
                'cantidad'              => 'Cantidad/dosis suministrada.',
                'presentacion'          => 'Presentación farmacéutica del medicamento.',
                'via_aplicacion'        => 'Vía de administración (etiqueta «Vía de Administración»: Oral / Venoso).',
                'frecuencia_suministro' => 'Frecuencia del suministro.',
                'dias_antibiotico'      => 'Días de antibiótico.',
                'fecha_suministro'      => 'Fecha de inicio del antibiótico (etiqueta «Fecha de Inicio»).',
                'mes'                   => 'Mes de la intervención (etiqueta «Mes»).',
                'fecha_intervencion'    => 'Fecha de la intervención PROA (etiqueta «Fecha de Intervención»).',
                'fecha_inicio_antibiotico' => 'Fecha de inicio del antibiótico.',
                'dosis_suministrada'    => 'Dosis suministrada (etiqueta «Dosis Suministrada»).',
                'sistema_internacional' => 'Unidad del sistema internacional.',
                'perfil_antimicrobiano' => 'Perfil del antimicrobiano.',
                'especialista_tratante' => 'Especialista tratante.',
                'diagnostico_infeccioso' => 'Diagnóstico infeccioso.',
                'dosis_adecuada'        => '¿La dosis es adecuada? (Sí / No / No aplica).',
                'fecha_fin_antibiotico' => 'Fecha de fin del antibiótico.',
                'tiempo_tratamiento'    => 'Tiempo de tratamiento.',
                'duracion_adecuada'     => '¿La duración es adecuada? (Sí / No / No aplica).',
                'cultivo_previo'        => '¿Hubo cultivo previo? (etiqueta «Cultivo Previo»).',
                'resultado_cultivo'     => 'Resultado del cultivo.',
                'solicitudes_pruebas'   => 'Solicitudes de pruebas especiales.',
                'oportunidad_reporte'   => 'Oportunidad del reporte.',
                'indicacion_terapia'    => 'Indicación de la terapia.',
                'tratamiento'           => 'Tipo de tratamiento (dirigido/empírico…).',
                'valoracion_grupo1'     => 'Valoración por infectología grupo 1.',
                'valoracion_uci'        => 'Valoración por infectología UCI/UCIN/neutropenia.',
                'fecha_valoracion'      => 'Fecha de la valoración.',
                'ajuste_prescripcion'   => 'Ajuste de la prescripción.',
                'adherencia_proa'       => 'Adherencia al PROA (Sí / No / Parcial / No aplica).',
                'adherencia_guias'      => '¿Adherencia a guías? (Sí / No).',
                'razon_no_adherencia'   => 'Razón de la no adherencia.',
                'observacion'           => 'Observación general de la intervención.',
                // ── Datos complementarios del caso ──
                'pais_origen'           => 'País de origen del paciente (etiqueta «País de origen»).',
                'departamento'          => 'Departamento de procedencia (DANE).',
                'municipio'             => 'Municipio de procedencia (DANE).',
                'diagnostico_ingreso'   => 'Diagnóstico de ingreso (etiqueta «Diagnóstico de ingreso»).',
                'asegurador'            => 'Asegurador (etiqueta «Asegurador»).',
                'peso'                  => 'Peso en kilogramos (etiqueta «Peso (kg)»).',
                'fecha_ingreso_hosp'    => 'Fecha de ingreso hospitalario (etiqueta «Fecha de ingreso»).',
                'microorganismo_2'      => 'Campo obsoleto (ya no se usa en el formulario).',
                'microorganismo_3'      => 'Campo obsoleto (ya no se usa en el formulario).',
                'fecha_quirurgica_previa' => 'Fecha de la cirugía previa a la infección.',
                'dias_entre_qx_e_infeccion' => 'Días entre la cirugía previa y la infección (calculado automáticamente).',
                'categoria_quirurgica'  => 'Categoría quirúrgica (etiqueta «Categoría Quirúrgica»).',
                'egreso'                => 'Estado de egreso (etiqueta «Egreso»: Vivo / Muerto / N/A).',
                'sitio'                 => 'Sitio de infección (etiqueta «Sitio»).',
                'tipo'                  => 'Tipo de infección (1 = intra, 2 = extra).',
                'clasificacion'         => 'Código de clasificación de la infección.',
                'clasificacion_texto'   => 'Clasificación en texto (calculado a partir de tipo + clasificación + sitio).',
                'especialidad_cirugia'  => 'Especialidad que realizó la cirugía.',
                'procedimiento_quirurgico' => 'Procedimiento quirúrgico realizado.',
                'bano_quirurgico'       => 'Baño quirúrgico (Sí / No).',
                'asepsia_quirurgica'    => 'Asepsia quirúrgica (Sí / No).',
                'profilaxis'            => 'Profilaxis (Sí / No).',
                'antibioticos_usados'   => 'Antibióticos usados (Sí / No).',
                'asa_preoperatoria'     => 'ASA preoperatoria (Sí / No).',
                'tiempo_quirurgico'     => 'Tiempo quirúrgico en minutos (etiqueta «Tiempo Quirúrgico»).',
                'tipo_cirugia'          => 'Tipo de cirugía (Electiva / Urgencia).',
                'clasificacion_cirugia' => 'Clasificación de la cirugía (L / LC / C / S).',
                'puntaje_nnis'          => 'Puntaje NNIS.',
                'revision_equipo'       => 'Revisión con equipo (quién revisó el caso).',
                'interconsulta_infectologia' => 'Interconsulta con infectología (Sí / No).',
                'comentarios'           => 'Comentarios (etiqueta «Comentarios»).',
                'fecha_insercion'       => 'Fecha de inserción (dispositivo).',
                'fecha_retiro'          => 'Fecha de retiro (dispositivo).',
                // ── Control ──
                'edicion_bloqueada'     => 'Indica si el formulario quedó bloqueado tras registrarse.',
                'registrado'            => 'Semáforo: indica si la muestra ya fue registrada (verde) o falta (rojo).',
            ] + $timestamps,

            'casos_microorganismo' => [
                'id'                    => 'Identificador interno del caso (llave primaria).',
                'paciente_id'           => 'Paciente dueño del caso.',
                'microorganismo'        => 'Microorganismo del caso.',
                'microorganismo_norm'   => 'Nombre normalizado (mayúsculas) para agrupar automáticamente.',
                'origen'                => 'Cómo se agrupó: «automatico» (importador) o «manual» (con los checkboxes).',
                'tipo_id'               => 'Tipo de documento del paciente.',
                'pais_origen'           => 'País de origen.',
                'departamento'          => 'Departamento de procedencia.',
                'municipio'             => 'Municipio de procedencia.',
                'diagnostico_ingreso'   => 'Diagnóstico de ingreso.',
                'asegurador'            => 'Asegurador.',
                'peso'                  => 'Peso en kilogramos.',
                'fecha_ingreso_hosp'    => 'Fecha de ingreso hospitalario.',
                'fecha_quirurgica_previa' => 'Fecha de cirugía previa.',
                'categoria_quirurgica'  => 'Categoría quirúrgica.',
                'egreso'                => 'Estado de egreso.',
                'sitio'                 => 'Sitio de infección.',
                'tipo'                  => 'Tipo de infección.',
                'clasificacion'         => 'Clasificación de la infección.',
                'clasificacion_texto'   => 'Clasificación en texto.',
                'especialidad_cirugia'  => 'Especialidad que realizó la cirugía.',
                'procedimiento_quirurgico' => 'Procedimiento quirúrgico.',
                'tiempo_quirurgico'     => 'Tiempo quirúrgico en minutos.',
                'bano_quirurgico'       => 'Baño quirúrgico.',
                'asepsia_quirurgica'    => 'Asepsia quirúrgica.',
                'profilaxis'            => 'Profilaxis.',
                'antibioticos_usados'   => 'Antibióticos usados.',
                'asa_preoperatoria'     => 'ASA preoperatoria.',
                'tipo_cirugia'          => 'Tipo de cirugía.',
                'clasificacion_cirugia' => 'Clasificación de la cirugía.',
                'puntaje_nnis'          => 'Puntaje NNIS.',
                'revision_equipo'       => 'Revisión con equipo.',
                'interconsulta_infectologia' => 'Interconsulta con infectología.',
                'fecha_insercion'       => 'Fecha de inserción.',
                'fecha_retiro'          => 'Fecha de retiro.',
                'comentarios'           => 'Comentarios.',
                'edicion_bloqueada'     => 'Indica si el caso quedó bloqueado tras registrarse.',
                'creado_por'            => 'Usuario que creó/agrupó el caso.',
            ] + $timestamps,

            'deta_procedimientos' => [
                'id'                => 'Identificador interno de la dosis (llave primaria).',
                'id_procedi'        => 'Enlace al lote de importación (encabezado).',
                'Cod_Episodio'      => 'Código del episodio de atención.',
                'Cod_Sala'          => 'Código de la sala.',
                'Nom_Sala'          => 'Nombre de la sala (etiqueta «Sala»).',
                'Num_Cama'          => 'Número de cama (etiqueta «Cama»).',
                'F_Ingreso'         => 'Fecha de ingreso (etiqueta «Fecha de Ingreso»).',
                'Cod_Eps'           => 'Código de la EPS (etiqueta «ID EPS»).',
                'Nom_Eps'           => 'Nombre de la EPS (etiqueta «EPS»).',
                'Hist_Clinica'      => 'Historia clínica (etiqueta «HC»).',
                'Tipo_Ident'        => 'Tipo de documento (etiqueta «Tipo ID»).',
                'Num_Ident'         => 'Número de documento (etiqueta «ID»); cruza con el paciente.',
                'Edad'              => 'Edad del paciente.',
                'Sexo'              => 'Sexo (etiqueta «Género»).',
                'Servicio'          => 'Servicio (por lo general vacío en la importación).',
                'Estado'            => 'Estado (por lo general vacío).',
                'Medico_Trata'      => 'Nombre (etiqueta «Nombre»).',
                'Cod_Diag'          => 'Código de diagnóstico.',
                'CIE10'             => 'Código CIE-10 (etiqueta «CIE-10»).',
                'Diagnostico'       => 'Diagnóstico (etiqueta «Diagnósticos»).',
                'Antimicrobiano'    => 'Antibiótico suministrado (estandarizado en MAYÚSCULA).',
                'Cantidad'          => 'Cantidad de la dosis.',
                'Presentacion'      => 'Presentación del medicamento.',
                'Via_Aplicacion'    => 'Vía de administración (Oral / Venoso).',
                'Tiem_Horas'        => 'Frecuencia en horas.',
                'Dias_Antibioticos' => 'Días de antibiótico.',
                'Fec_Sumistro'      => 'Fecha de inicio del antibiótico (de la columna «Fecha inicio antibiótico» del Excel).',
                'Ho_Sumisnistro'    => 'Hora del suministro.',
            ] + $timestamps,

            'intervenciones_proa' => [
                'id'                    => 'Identificador interno de la intervención (llave primaria).',
                'id_deta_procedimiento' => 'Enlace a la dosis (deta_procedimientos) intervenida.',
                'mes'                   => 'Mes de la intervención.',
                'fecha_intervencion'    => 'Fecha de la intervención.',
                'fecha_inicio_antibiotico' => 'Fecha de inicio del antibiótico.',
                'dosis_suministrada'    => 'Dosis suministrada.',
                'id_sis_internacional'  => 'Unidad del sistema internacional (catálogo).',
                'id_frecuencia'         => 'Frecuencia (catálogo).',
                'id_perfil_antimicrobiano' => 'Perfil antimicrobiano (catálogo).',
                'id_esp_tratante'       => 'Especialista tratante (catálogo).',
                'id_diag_infeccioso'    => 'Diagnóstico infeccioso (catálogo).',
                'dosis_adecuada'        => '¿Dosis adecuada? (Sí / No / No aplica).',
                'fecha_fin_antibiotico' => 'Fecha de fin del antibiótico.',
                'tiempo_tratamiento'    => 'Tiempo de tratamiento.',
                'duracion_adecuada'     => '¿Duración adecuada? (Sí / No / No aplica).',
                'cultivo_previo'        => '¿Cultivo previo? (Sí / No).',
                'fecha_muestra'         => 'Fecha de la muestra.',
                'id_tipo_muestra'       => 'Tipo de muestra (catálogo).',
                'id_resultado'          => 'Resultado del cultivo (catálogo).',
                'id_microorganismo'     => 'Microorganismo (catálogo).',
                'id_pantimicrobiano'    => 'Perfil de antimicrobiano (catálogo).',
                'solicitudes_pruebas'   => 'Solicitudes de pruebas especiales.',
                'oportunidad_reporte'   => 'Oportunidad del reporte.',
                'id_indicacion_terapia' => 'Indicación de la terapia (catálogo).',
                'id_tratamiento'        => 'Tratamiento (catálogo).',
                'valoracion_grupo1'     => 'Valoración por infectología grupo 1.',
                'valoracion_uci'        => 'Valoración por infectología UCI.',
                'fecha_valoracion'      => 'Fecha de la valoración.',
                'ajuste_prescripcion'   => 'Ajuste de la prescripción.',
                'adherencia_proa'       => 'Adherencia al PROA.',
                'adherencia_guias'      => '¿Adherencia a guías?',
                'razon_no_adherencia'   => 'Razón de la no adherencia.',
                'caso_cerrado'          => '¿Caso cerrado?',
                'mortalidad'            => '¿Mortalidad?',
                'observacion'           => 'Observación general.',
                'edicion_bloqueada'     => 'Indica si la intervención quedó bloqueada tras registrarse.',
            ] + $timestamps,

            'actividades' => [
                'id'               => 'Identificador interno del evento (llave primaria).',
                'user_id'          => 'Usuario que realizó la acción.',
                'user_nombre'      => 'Nombre del usuario (guardado para consulta).',
                'tipo'             => 'Tipo de actividad (epidemiología, proa, importación…).',
                'accion'           => 'Acción realizada (crear, actualizar, importar, agrupar, vaciar…).',
                'descripcion'      => 'Descripción legible de lo que pasó.',
                'referencia_tabla' => 'Tabla afectada por la acción.',
                'referencia_id'    => 'Id del registro afectado.',
                'paciente'         => 'Paciente relacionado (si aplica).',
                'ip'               => 'Dirección IP desde donde se hizo la acción.',
            ] + $timestamps,
        ];
    }
}
