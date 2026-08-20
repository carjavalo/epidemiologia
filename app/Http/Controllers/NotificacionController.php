<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;

class NotificacionController extends Controller
{
    /** Marca todas las notificaciones como leídas. */
    public function leerTodas()
    {
        Notificacion::where('leida', false)->update(['leida' => true]);
        return back()->with('success', 'Notificaciones marcadas como leídas.');
    }

    /** Marca una notificación como leída y redirige a su enlace (si tiene). */
    public function leer(Notificacion $notificacion)
    {
        $notificacion->update(['leida' => true]);

        return redirect($notificacion->url ?: url()->previous());
    }

    /** Elimina una notificación. */
    public function eliminar(Notificacion $notificacion)
    {
        $notificacion->delete();
        return back();
    }

    /** Vacía (elimina) todas las notificaciones. */
    public function vaciar()
    {
        Notificacion::query()->delete();
        return back()->with('success', 'Notificaciones vaciadas.');
    }
}
