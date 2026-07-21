<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\EncabezadoProcedimiento;
use App\Models\IntervencionProa;
use App\Models\Procedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProcedimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedimientos = EncabezadoProcedimiento::orderBy('fecha_procedimiento', 'desc')->paginate(10);
        $totalPacientes = \App\Models\Paciente::count();
        $totalSeguimientos = \App\Models\EpidemiologiaRegistro::count();
        $totalDetallesProa = Procedimiento::count();
        $totalIntervenciones = IntervencionProa::count();

        return view('procedimientos.index', compact(
            'procedimientos', 'totalPacientes', 'totalSeguimientos',
            'totalDetallesProa', 'totalIntervenciones'
        ));
    }

    /**
     * Vacía por completo la base de PROA: intervenciones, detalles y lotes.
     * Acción destructiva e irreversible; solo administradores (ver rutas).
     */
    public function vaciarProa()
    {
        try {
            $detalles       = Procedimiento::count();
            $intervenciones = IntervencionProa::count();
            $lotes          = EncabezadoProcedimiento::count();

            DB::transaction(function () {
                // Las intervenciones referencian los detalles; los detalles al lote.
                IntervencionProa::query()->delete();
                Procedimiento::query()->delete();
                EncabezadoProcedimiento::query()->delete();
            });

            Actividad::registrar([
                'tipo'        => 'importacion',
                'accion'      => 'vaciar',
                'descripcion' => "Vació la base de PROA: {$detalles} detalle(s), {$intervenciones} intervención(es) y {$lotes} lote(s) eliminados.",
            ]);

            return redirect()->route('procedimientos.index')->with(
                'success',
                "Base de PROA vaciada: se eliminaron {$detalles} detalle(s), {$intervenciones} intervención(es) y {$lotes} lote(s)."
            );
        } catch (\Exception $e) {
            Log::error('Error vaciando PROA: ' . $e->getMessage());

            return redirect()->route('procedimientos.index')->with('error', 'No se pudo vaciar la base de PROA: ' . $e->getMessage());
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $procedimiento = EncabezadoProcedimiento::with('detalles')->findOrFail($id);
        return view('procedimientos.show', compact('procedimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $procedimiento = EncabezadoProcedimiento::findOrFail($id);
        return view('procedimientos.edit', compact('procedimiento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'fecha_procedimiento' => 'required|date',
            'Nom_procedimiento' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('procedimientos.edit', $id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $procedimiento = EncabezadoProcedimiento::findOrFail($id);
        $procedimiento->update($request->all());

        return redirect()->route('procedimientos.index')
                        ->with('success', 'Procedimiento actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            // Eliminar primero los detalles asociados
            Procedimiento::where('id_procedi', $id)->delete();
            
            // Luego eliminar el encabezado
            $procedimiento = EncabezadoProcedimiento::findOrFail($id);
            $procedimiento->delete();
            
            DB::commit();
            
            return redirect()->route('procedimientos.index')
                        ->with('success', 'Procedimiento y sus detalles eliminados exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('procedimientos.index')
                        ->with('error', 'Error al eliminar el procedimiento: ' . $e->getMessage());
        }
    }

    /**
     * Inserta datos de procedimiento en formato pipe (|)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function insertarDetalle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_pipe' => 'required|string',
            'id_procedimiento' => 'nullable|exists:encabezados_procedimientos,id_procedimiento'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $dataPipe = $request->data_pipe;
            $idProcedimiento = $request->id_procedimiento;
            
            // Si no se proporciona un id_procedimiento, crear uno nuevo
            if (empty($idProcedimiento)) {
                $encabezado = EncabezadoProcedimiento::create([
                    'fecha_procedimiento' => now(),
                    'Nom_procedimiento' => 'Procedimiento Automático ' . date('Y-m-d H:i:s')
                ]);
                $idProcedimiento = $encabezado->id_procedimiento;
            }
            
            // Procesar los datos en formato pipe
            $procedimiento = Procedimiento::insertarDesdeFormatoPipe($dataPipe, $idProcedimiento);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Datos insertados correctamente',
                'data' => [
                    'id_procedimiento' => $idProcedimiento,
                    'id_detalle' => $procedimiento->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al insertar datos en formato pipe: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesa un archivo de texto con datos de procedimientos
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function procesarArchivoTxt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo_txt' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['txt', 'sql'])) {
                        $fail('El archivo debe tener extensión .txt o .sql');
                    }
                },
            ],
            'nom_procedimiento' => 'required|string|max:255',
            'id_procedimiento' => 'nullable|exists:encabezados_procedimientos,id_procedimiento'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        // Obtener el contenido del archivo
        $archivo = $request->file('archivo_txt');
        $contenido = file_get_contents($archivo->getRealPath());
        // Convertir de Windows-1252 / Latin-1 a UTF-8 para soportar caracteres como Ñ, tildes, etc.
        if (!mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }
        
        DB::beginTransaction();
        
        try {
            $idProcedimiento = $request->id_procedimiento;
            $resultados = [];
            
            // Si no se proporciona un id_procedimiento, crear uno nuevo
            if (empty($idProcedimiento)) {
                $encabezado = EncabezadoProcedimiento::create([
                    'fecha_procedimiento' => now(),
                    'Nom_procedimiento' => $request->nom_procedimiento
                ]);
                $idProcedimiento = $encabezado->id_procedimiento;
            }
            
            // Detectar el formato del archivo: SQL o Pipe
            if (strpos($contenido, 'INSERT INTO') !== false) {
                // Procesar como formato SQL
                $resultados = $this->procesarFormatoSQL($contenido, $idProcedimiento);
            } else {
                // Procesar como formato Pipe
                // Usar preg_split para soportar tanto \r\n (Windows) como \n (Unix)
                $lineas = preg_split('/\r?\n/', $contenido);
                $lineas = array_filter($lineas, function($linea) {
                    return !empty(trim($linea));
                });
                
                foreach ($lineas as $linea) {
                    $procedimiento = Procedimiento::insertarDesdeFormatoPipe(trim($linea), $idProcedimiento);
                    $resultados[] = [
                        'id_detalle' => $procedimiento->id,
                        'linea' => trim($linea)
                    ];
                }
            }
            
            DB::commit();
            
            return redirect()->back()
                        ->with('success', 'Archivo procesado correctamente. Se importaron ' . count($resultados) . ' registros.')
                        ->with('resultados', $resultados);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al procesar archivo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                        ->with('error', 'Error al procesar el archivo: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Procesa datos en formato SQL INSERT
     * 
     * @param string $contenido
     * @param int $idProcedimiento
     * @return array
     */
    private function procesarFormatoSQL($contenido, $idProcedimiento)
    {
        $resultados = [];
        
        // Extraer los bloques VALUES (...); que contienen los datos
        preg_match_all('/VALUES\s*\(\s*(.*?)\s*\)\s*;/s', $contenido, $matches);
        
        if (empty($matches[1])) {
            throw new \Exception('No se encontraron valores en el formato SQL.');
        }
        
        foreach ($matches[1] as $valuesBlock) {
            // Eliminar comillas simples y dobles
            $valores = str_replace(["'", '"'], '', $valuesBlock);
            
            // Dividir los valores por comas
            $campos = array_map('trim', explode(',', $valores));
            
            // Extraer valores según la estructura SQL proporcionada
            $datos = [
                'id_procedi' => $idProcedimiento,
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
                'Nombre' => isset($campos[10]) ? $campos[10] : null,
                'apellido1' => isset($campos[11]) ? $campos[11] : null,
                'apellido2' => isset($campos[12]) ? $campos[12] : null,
                'Sexo' => isset($campos[13]) ? $campos[13] : null,
                'Fec_Nacimiento' => isset($campos[14]) ? $campos[14] : null,
                'CIE10' => isset($campos[15]) ? $campos[15] : null,
                'Diagnostico' => isset($campos[16]) ? $campos[16] : null,
                'Antimicrobiano' => isset($campos[17]) ? $campos[17] : null,
                'Cantidad' => isset($campos[18]) ? $campos[18] : null,
                'Presentacion' => isset($campos[19]) ? $campos[19] : null,
                'Via_Aplicacion' => isset($campos[20]) ? $campos[20] : null,
                'Tiem_Horas' => isset($campos[21]) ? $campos[21] : null,
                'Dias_Antibioticos' => isset($campos[22]) ? $campos[22] : null,
                'Fec_Sumistro' => isset($campos[23]) ? $campos[23] : null,
                'Ho_Sumisnistro' => isset($campos[24]) ? $campos[24] : null
            ];
            
            // Calcular la edad a partir de la fecha de nacimiento
            if (!empty($datos['Fec_Nacimiento'])) {
                try {
                    $fechaNac = new \DateTime($datos['Fec_Nacimiento']);
                    $hoy = new \DateTime();
                    $datos['Edad'] = $hoy->diff($fechaNac)->y;
                } catch (\Exception $e) {
                    // Si hay un error, no asignar edad
                }
            }
            
            // Fusionar nombres y apellidos en Medico_Trata para mantener compatible con la estructura existente
            $nombreCompleto = trim(implode(' ', array_filter([
                $datos['Nombre'] ?? '',
                $datos['apellido1'] ?? '',
                $datos['apellido2'] ?? ''
            ])));
            
            if (!empty($nombreCompleto)) {
                $datos['Medico_Trata'] = $nombreCompleto;
            }
            
            // Filtrar valores nulos o vacíos
            $datos = array_filter($datos, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Crear el registro
            $procedimiento = Procedimiento::create($datos);
            
            $resultados[] = [
                'id_detalle' => $procedimiento->id,
                'linea' => "SQL: " . substr($valuesBlock, 0, 100) . (strlen($valuesBlock) > 100 ? '...' : '')
            ];
        }
        
        return $resultados;
    }
}
