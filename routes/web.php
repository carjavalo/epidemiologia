<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProcedimientoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('aplicativos.index');
    }
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de usuarios
    Route::resource('users', UserController::class)->middleware('can:admin');

    // Centro de aplicativos: primera pantalla despues del login
    Route::get('aplicativos', fn () => view('aplicativos.index'))->name('aplicativos.index');

    // Módulo "Base de datos": diccionario de datos (documentación de tablas)
    Route::get('base-datos', [App\Http\Controllers\BaseDatosController::class, 'index'])->name('base-datos.index');

    // Conteo de IAAS por rango de fechas personalizable (req. 12)
    Route::get('iaas/conteo', [App\Http\Controllers\IaasController::class, 'conteo'])->name('iaas.conteo');

    // Notificaciones (campana de la barra superior)
    Route::post('notificaciones/leer-todas', [App\Http\Controllers\NotificacionController::class, 'leerTodas'])->name('notificaciones.leerTodas');
    Route::get('notificaciones/{notificacion}/leer', [App\Http\Controllers\NotificacionController::class, 'leer'])->name('notificaciones.leer');
    Route::delete('notificaciones/{notificacion}', [App\Http\Controllers\NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
    Route::delete('notificaciones', [App\Http\Controllers\NotificacionController::class, 'vaciar'])->name('notificaciones.vaciar');
    
    // Rutas de procedimientos (solo lectura)
    Route::resource('procedimientos', ProcedimientoController::class)->except(['create', 'store']);

    // Ruta para insertar datos de procedimiento en formato pipe
    Route::post('/procedimientos/insertar-detalle', [App\Http\Controllers\ProcedimientoController::class, 'insertarDetalle'])
        ->name('procedimientos.insertar-detalle');

    // Ruta para procesar archivo de texto con datos de procedimientos
    Route::post('/procedimientos/procesar-archivo', [App\Http\Controllers\ProcedimientoController::class, 'procesarArchivoTxt'])
        ->name('procedimientos.procesar-archivo');



    // Ruta de registros (vista de pacientes y medicamentos) — abierta a todos los autenticados
    Route::get('registros', [App\Http\Controllers\RegistrosController::class, 'index'])->name('registros.index');

    // ── SECCIÓN CONFIGURACIÓN (catálogos): SOLO ADMINISTRADORES ──────────
    Route::middleware('can:admin')->group(function () {
        // Rutas de indicaciones terapéuticas
        Route::resource('indicaciones', App\Http\Controllers\IndicacionTerapiaController::class);

        // Rutas de sistema internacional
        Route::resource('sis-internacional', App\Http\Controllers\SisInternacionalController::class);

        // Rutas de frecuencia
        Route::resource('frecuencia', App\Http\Controllers\FrecuenciaController::class);

        // Rutas de perfil antimicrobiano
        Route::resource('pantimicrobiano', App\Http\Controllers\PantimicrobianoController::class);

        // Rutas de especialidad tratante
        Route::resource('esp-tratante', App\Http\Controllers\EspTratanteController::class);

        // Rutas de categoría quirúrgica
        Route::resource('categoria-quirurgica', App\Http\Controllers\CategoriaQuirurgicaController::class)
            ->parameters(['categoria-quirurgica' => 'categoriaQuirurgica']);

        // Rutas de diagnósticos (catálogo CIE-10)
        Route::resource('diagnosticos', App\Http\Controllers\DiagnosticoController::class);

        // Rutas de tipo de muestra
        Route::resource('tip-muestra', App\Http\Controllers\TipMuestraController::class);

        // Catálogo de servicios (ubicaciones estandarizadas)
        Route::resource('servicios', App\Http\Controllers\ServicioController::class);

        // Catálogo del campo SITIO del formulario de epidemiología
        Route::resource('sitios', App\Http\Controllers\SitioController::class);

        // Equivalencias: texto crudo de la fuente -> valor estandarizado.
        // 'pendientes' y 'mapear' van antes del resource para que no los capture
        // el comodín {equivalencia}.
        Route::get('equivalencias/pendientes', [App\Http\Controllers\EquivalenciaController::class, 'pendientes'])
            ->name('equivalencias.pendientes');
        Route::post('equivalencias/mapear', [App\Http\Controllers\EquivalenciaController::class, 'mapear'])
            ->name('equivalencias.mapear');
        Route::resource('equivalencias', App\Http\Controllers\EquivalenciaController::class)
            ->except(['show']);

        // Rutas de diagnóstico infeccioso
        Route::resource('diag-infeccioso', App\Http\Controllers\DiagInfecciosoController::class);

        // Rutas de resultado
        Route::resource('resultado', App\Http\Controllers\ResultadoController::class);

        // Rutas de microorganismo
        Route::resource('microorganismo', App\Http\Controllers\MicroorganismoController::class);

        // Rutas de tratamiento
        Route::resource('tratamiento', App\Http\Controllers\TratamientoController::class);

        // Rutas de perfiles (definidas manualmente para evitar problemas de pluralización)
        Route::get('perfiles', [App\Http\Controllers\PerfilController::class, 'index'])->name('perfiles.index');
        Route::get('perfiles/create', [App\Http\Controllers\PerfilController::class, 'create'])->name('perfiles.create');
        Route::post('perfiles', [App\Http\Controllers\PerfilController::class, 'store'])->name('perfiles.store');
        Route::get('perfiles/{perfil}', [App\Http\Controllers\PerfilController::class, 'show'])->name('perfiles.show');
        Route::get('perfiles/{perfil}/edit', [App\Http\Controllers\PerfilController::class, 'edit'])->name('perfiles.edit');
        Route::put('perfiles/{perfil}', [App\Http\Controllers\PerfilController::class, 'update'])->name('perfiles.update');
        Route::delete('perfiles/{perfil}', [App\Http\Controllers\PerfilController::class, 'destroy'])->name('perfiles.destroy');

        // Rutas de plantillas de observaciones (definidas manualmente para evitar problemas de pluralización)
        Route::get('plantillas-observaciones', [App\Http\Controllers\PlantillaObservacionController::class, 'index'])->name('plantillas-observaciones.index');
        Route::get('plantillas-observaciones/create', [App\Http\Controllers\PlantillaObservacionController::class, 'create'])->name('plantillas-observaciones.create');
        Route::post('plantillas-observaciones', [App\Http\Controllers\PlantillaObservacionController::class, 'store'])->name('plantillas-observaciones.store');
        Route::get('plantillas-observaciones/{plantillaObservacion}', [App\Http\Controllers\PlantillaObservacionController::class, 'show'])->name('plantillas-observaciones.show');
        Route::get('plantillas-observaciones/{plantillaObservacion}/edit', [App\Http\Controllers\PlantillaObservacionController::class, 'edit'])->name('plantillas-observaciones.edit');
        Route::put('plantillas-observaciones/{plantillaObservacion}', [App\Http\Controllers\PlantillaObservacionController::class, 'update'])->name('plantillas-observaciones.update');
        Route::delete('plantillas-observaciones/{plantillaObservacion}', [App\Http\Controllers\PlantillaObservacionController::class, 'destroy'])->name('plantillas-observaciones.destroy');
    });

    // Rutas de intervenciones PROA
    Route::post('intervenciones-proa/guardar', [App\Http\Controllers\IntervencionProaController::class, 'guardar'])->name('intervenciones-proa.guardar');
    Route::get('intervenciones-proa/{id}', [App\Http\Controllers\IntervencionProaController::class, 'obtener'])->name('intervenciones-proa.obtener');

    // Rutas de epidemiología
    Route::get('epidemiologia', [App\Http\Controllers\EpidemiologiaController::class, 'index'])->name('epidemiologia.index');
    Route::post('epidemiologia/guardar', [App\Http\Controllers\EpidemiologiaController::class, 'guardar'])->name('epidemiologia.guardar');
    Route::post('epidemiologia/casos/agrupar', [App\Http\Controllers\EpidemiologiaController::class, 'agruparCasos'])->name('epidemiologia.casos.agrupar');
    Route::get('epidemiologia/obtener/{idProcedimiento}', [App\Http\Controllers\EpidemiologiaController::class, 'obtener'])->name('epidemiologia.obtener');

    // Vaciado de bases (acciones destructivas): solo administradores
    Route::delete('epidemiologia/vaciar', [App\Http\Controllers\EpidemiologiaController::class, 'vaciar'])
        ->name('epidemiologia.vaciar')->middleware('can:admin');
    Route::delete('proa/vaciar', [App\Http\Controllers\ProcedimientoController::class, 'vaciarProa'])
        ->name('proa.vaciar')->middleware('can:admin');

    // Importación de seguimiento microbiológico: TXT epidemiología + TXT PROA
    Route::get('seguimiento-microbiologico/importar', [App\Http\Controllers\SeguimientoMicrobiologicoController::class, 'formularioImportacion'])->name('seguimiento.importar');
    Route::post('seguimiento-microbiologico/importar', [App\Http\Controllers\SeguimientoMicrobiologicoController::class, 'importar'])->name('seguimiento.importar.procesar');
});

require __DIR__.'/auth.php';
