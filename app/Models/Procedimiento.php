<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'deta_procedimientos';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_procedi',
        'Cod_Episodio',
        'Cod_Sala',
        'Nom_Sala',
        'Num_Cama',
        'F_Ingreso',
        'Cod_Eps',
        'Nom_Eps',
        'Hist_Clinica',
        'Tipo_Ident',
        'Num_Ident',
        'Edad',
        'Sexo',
        'Servicio',
        'Estado',
        'Medico_Trata',
        'Cod_Diag',
        'CIE10',
        'Diagnostico',
        'Antimicrobiano',
        'Cantidad',
        'Presentacion',
        'Via_Aplicacion',
        'Tiem_Horas',
        'Dias_Antibioticos',
        'Fec_Sumistro',
        'Ho_Sumisnistro',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'F_Ingreso' => 'datetime',
        'Fec_Sumistro' => 'date',
        'Ho_Sumisnistro' => 'string',
        'Edad' => 'integer',
        'Cod_Episodio' => 'integer',
        'Hist_Clinica' => 'integer',
        'id_procedi' => 'integer',
    ];
    
    /**
     * Obtiene el encabezado de procedimiento asociado a este detalle.
     */
    public function encabezado()
    {
        return $this->belongsTo(EncabezadoProcedimiento::class, 'id_procedi', 'id_procedimiento');
    }

    /**
     * Método estático para insertar datos desde una cadena con formato separado por pipe (|)
     * 
     * @param string $dataPipe La cadena con los datos separados por |
     * @param int $idProcedi ID del encabezado de procedimiento relacionado
     * @return Procedimiento
     */
    public static function insertarDesdeFormatoPipe($dataPipe, $idProcedi = null)
    {
        // Dividir la cadena por el delimitador |
        $campos = explode('|', $dataPipe);

        // Eliminar espacios en blanco al inicio y fin de cada campo
        $campos = array_map('trim', $campos);

        // Si el campo es una cadena vacía, establecerlo como null
        $campos = array_map(function ($valor) {
            return $valor === '' ? null : $valor;
        }, $campos);

        return self::insertarDesdeCampos($campos, $idProcedi);
    }

    /**
     * Inserta un detalle de procedimiento a partir de un arreglo de campos ya
     * separados (viene de TXT/CSV/Excel). Mismo orden de columnas 0..19.
     *
     * @param  array  $campos  Campos en el orden del formato pipe
     * @param  int|null  $idProcedi  ID del encabezado de procedimiento
     * @param  array|null  $recortados  Se llena con los campos que hubo que recortar.
     * @return Procedimiento
     */
    public static function insertarDesdeCampos(array $campos, $idProcedi = null, ?array &$recortados = null)
    {
        // Formato de campos según la imagen:
        // 0: Cod_Episodio (1093707)
        // 1: Cod_Sala (70)
        // 2: Nom_Sala (TRANSICION CONSULTA URGENCIAS)
        // 3: Num_Cama (vacío)
        // 4: F_Ingreso (2023/11/20 20:00:30)
        // 5: Cod_Eps (901126115)
        // 6: Nom_Eps (COMPENSAR EPS SAS)
        // 7: Hist_Clinica (2909657)
        // 8: Tipo_Ident (CC)
        // 9: Num_Ident (51949289)
        // 10: Primer Nombre (LUZ MYRIAM)
        // 11: Segundo Nombre (vacío)
        // 12: Apellidos (SOLER RUIZ)
        // 13: Sexo (F)
        // 14: Fecha Nacimiento (1970/02/10)
        // 15: CIE10 (Z321)
        // Formato real del archivo TXT (campos separados por |):
        // 0:  Cod_Episodio     (12453)
        // 1:  Cod_Sala         (75)
        // 2:  Nom_Sala         (CIRUGIA PEDIATRICA ANA FRANK)
        // 3:  Num_Cama         (2503)
        // 4:  F_Ingreso        (2025-07-21 13:19:00)
        // 5:  Cod_Eps          (901021565S)
        // 6:  Nom_Eps          (EMSSANAR EPS SAS)
        // 7:  Hist_Clinica     (2691413)
        // 8:  Tipo_Ident       (RC)
        // 9:  Num_Ident        (1112935959)
        // 10: Nombre           (ERICK)
        // 11: Apellido1        (ARBOLEDA)
        // 12: Apellido2        (CARVAJAL)
        // 13: Sexo             (M)
        // 14: Fec_Nacimiento   (2023/01/04)
        // 15: CIE10            (K613)
        // 16: Diagnostico      (ABSCESO ISQUIORRECTAL)
        // 17: Antimicrobiano   (Piperacilina / tazobactam vial x 4.5 gr)
        // 18: Instrucciones    (1400 MILIGRAMOS, ENDOVENOSA, Cada 8 horas, por 24 HORAS)
        // 19: Fecha+hora sumi  (2025-08-03 00:02:56)

        // Extraer nombre completo del paciente (campos 10, 11, 12)
        $nombre    = isset($campos[10]) ? $campos[10] : null;
        $apellido1 = isset($campos[11]) ? $campos[11] : null;
        $apellido2 = isset($campos[12]) ? $campos[12] : null;

        $nombreCompleto = trim(implode(' ', array_filter([$nombre, $apellido1, $apellido2])));

        // Extraer componentes de dosis del campo 18:
        // Formato: "CANTIDAD UNIDAD, VIA, FRECUENCIA, DURACION"
        // Ej:      "1400 MILIGRAMOS, ENDOVENOSA, Cada 8 horas, por 24 HORAS"
        $instruccionesMedicacion = isset($campos[18]) ? $campos[18] : null;
        $cantidad      = null;
        $presentacion  = null;
        $viaAplicacion = null;
        $tiempoHoras   = null;
        $diasAntibioticos = null;

        if ($instruccionesMedicacion) {
            $partesMedicacion = array_map('trim', explode(',', $instruccionesMedicacion));
            $cantidad      = $partesMedicacion[0] ?? null;                   // "1400 MILIGRAMOS"
            $viaAplicacion = $partesMedicacion[1] ?? null;                   // "ENDOVENOSA"
            $tiempoHoras   = $partesMedicacion[2] ?? null;                   // "Cada 8 horas"
            $diasAntibioticos = $partesMedicacion[3] ?? null;                // "por 24 HORAS"
        }
        
        // Calcular la edad a partir de la fecha de nacimiento si está presente
        $fechaNacimiento = isset($campos[14]) ? $campos[14] : null;
        $edad = null;
        
        if ($fechaNacimiento) {
            try {
                $fechaNac = new \DateTime($fechaNacimiento);
                $hoy = new \DateTime();
                $edad = $hoy->diff($fechaNac)->y;
            } catch (\Exception $e) {
                // Si hay un error, dejar la edad como null
            }
        }
        
        // Crear un array con los datos a guardar
        $datos = [
            'id_procedi' => $idProcedi,
            'Cod_Episodio' => isset($campos[0]) ? $campos[0] : null,
            'Cod_Sala' => isset($campos[1]) ? $campos[1] : null,
            'Nom_Sala' => isset($campos[2]) ? $campos[2] : null,
            'Num_Cama' => isset($campos[3]) ? $campos[3] : null,
            'F_Ingreso' => isset($campos[4]) ? $campos[4] : null,
            'Cod_Eps' => isset($campos[5]) ? $campos[5] : null,
            'Nom_Eps' => isset($campos[6]) ? $campos[6] : null,
            'Hist_Clinica' => isset($campos[7]) ? $campos[7] : null,
            'Tipo_Ident' => isset($campos[8]) ? $campos[8] : null,
            'Num_Ident' => isset($campos[9]) ? $campos[9] : null,
            'Edad' => $edad,
            'Sexo' => isset($campos[13]) ? self::normalizarSexo($campos[13]) : null,
            'Servicio' => null, // No está en el formato del ejemplo
            'Estado' => null, // No está en el formato del ejemplo
            'Medico_Trata' => $nombreCompleto, // Nombre del paciente (podría ser ajustado)
            'Cod_Diag' => isset($campos[15]) ? $campos[15] : null,
            'CIE10' => isset($campos[15]) ? $campos[15] : null,
            'Diagnostico' => isset($campos[16]) ? $campos[16] : null,
            'Antimicrobiano' => isset($campos[17]) ? self::extractNombreMedicamento($campos[17]) : null,
            'Cantidad' => $cantidad,
            'Presentacion' => isset($campos[17]) ? $campos[17] : null,
            'Via_Aplicacion' => $viaAplicacion,
            'Tiem_Horas' => $tiempoHoras,
            'Dias_Antibioticos' => $diasAntibioticos,
            'Fec_Sumistro' => null, // Será extraído del campo 19
            'Ho_Sumisnistro' => null, // Será extraído del campo 19
        ];
        
        // Si Fecha suministro tiene fecha y hora, dividirlo
        if (isset($campos[19]) && !empty($campos[19])) {
            $fechaHora = explode(' ', $campos[19]);
            $datos['Fec_Sumistro'] = isset($fechaHora[0]) ? $fechaHora[0] : null;
            $datos['Ho_Sumisnistro'] = isset($fechaHora[1]) ? $fechaHora[1] : null;
        }
        
        // Filtrar valores nulos o vacíos
        $datos = array_filter($datos, function($value) {
            return $value !== null && $value !== '';
        });

        // El Excel de origen a veces trae observaciones clínicas en la columna
        // del medicamento. Sin este recorte una sola celda aborta la
        // importación completa; los campos recortados quedan reportados.
        $datos = self::recortarACapacidad($datos, $recortados);

        // Crear y devolver el nuevo registro
        return self::create($datos);
    }

    /**
     * Ancho máximo de las columnas de texto de deta_procedimientos.
     */
    private const LIMITES = [
        'Cod_Sala'          => 20,
        'Nom_Sala'          => 255,
        'Num_Cama'          => 20,
        'Cod_Eps'           => 30,
        'Nom_Eps'           => 255,
        'Tipo_Ident'        => 5,
        'Num_Ident'         => 20,
        'Servicio'          => 100,
        'Estado'            => 50,
        'Medico_Trata'      => 255,
        'Cod_Diag'          => 10,
        'CIE10'             => 10,
        'Diagnostico'       => 500,
        'Antimicrobiano'    => 100,
        'Cantidad'          => 100,
        'Presentacion'      => 500,
        'Via_Aplicacion'    => 100,
        'Tiem_Horas'        => 100,
        'Dias_Antibioticos' => 100,
    ];

    /**
     * Recorta los valores que exceden el ancho de su columna.
     *
     * @param  array  $datos
     * @param  array|null  $recortados  Se llena con [campo => longitud original].
     * @return array
     */
    public static function recortarACapacidad(array $datos, ?array &$recortados = null): array
    {
        foreach (self::LIMITES as $campo => $max) {
            if (!isset($datos[$campo]) || !is_string($datos[$campo])) {
                continue;
            }

            $largo = mb_strlen($datos[$campo]);
            if ($largo > $max) {
                $recortados[$campo] = $largo;
                $datos[$campo] = mb_substr($datos[$campo], 0, $max);
            }
        }

        return $datos;
    }

    /**
     * Extrae solo el nombre genérico del medicamento, eliminando
     * la forma farmacéutica y la concentración.
     *
     * Ejemplos:
     *   "Piperacilina / tazobactam vial x 4.5 gr"  → "Piperacilina/tazobactam"
     *   "Amikacina sulfato ampolla x 500 mg"        → "Amikacina sulfato"
     *   "Linezolid 2mg/ml. bolsa por 600 mg"        → "Linezolid"
     *   "Trimetoprim - sulfametoxazol tableta x..." → "Trimetoprim/sulfametoxazol"
     */
    public static function extractNombreMedicamento(?string $texto): ?string
    {
        if (empty($texto)) {
            return null;
        }

        // Palabras clave que marcan el inicio de la forma farmacéutica
        $formasFarmaceuticas = [
            'vial', 'ampolla', 'tableta', 'tabletas', 'bolsa', 'capsula', 'cápsula',
            'capsulas', 'cápsulas', 'solucion', 'solución', 'inyectable', 'crema',
            'parche', 'suspension', 'suspensión', 'jarabe', 'comprimido', 'comprimidos',
            'polvo', 'gel', 'ovulo', 'óvulo', 'supositorio',
        ];

        $nombre = $texto;

        // Cortar desde la primera forma farmacéutica hasta el final (incluye "x 4.5 gr", etc.)
        $pattern = '/\s+(' . implode('|', $formasFarmaceuticas) . ').*$/i';
        $nombre = preg_replace($pattern, '', $nombre, 1);
        // Si quedó concentración pegada al nombre (ej: "Linezolid 2mg/ml."), cortarla también
        $nombre = preg_replace('/\s+\d+[\d.,]*\s*(mg|mcg|g|ml|ui|iu)\/?(ml|mg)?[\.\s].*/i', '', $nombre);

        // Normalizar separadores de combinaciones: " / " y " - " → "/"
        $nombre = preg_replace('/\s*\/\s*/', '/', $nombre);
        $nombre = preg_replace('/\s+\-\s+/', '/', $nombre);

        // Estandarizar en MAYÚSCULA para que un mismo antibiótico no se
        // divida en varios grupos por diferencias de capitalización.
        return mb_strtoupper(trim($nombre), 'UTF-8');
    }

    public static function normalizarSexo(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }
        $v = mb_strtoupper(trim($valor));
        if (str_starts_with($v, 'M')) {
            return 'M';
        }
        if (str_starts_with($v, 'F')) {
            return 'F';
        }
        return mb_substr($v, 0, 1); // Retornar solo el primer caracter como fallback
    }
}
