<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('welcome');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // El registro solo pide usuario + contraseña (sin correo). Los errores
        // van a un "bag" propio ('registro') para que la pantalla mantenga
        // activa la pestaña de registro y no la de inicio de sesión.
        Validator::make($request->all(), [
            'usuario'  => ['required', 'string', 'max:100', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(6)],
        ])->validateWithBag('registro');

        $user = User::create([
            'usuario'  => $request->usuario,
            'name'     => $request->usuario, // se mantiene para compatibilidad (avatar/nombre AdminLTE)
            'password' => Hash::make($request->password),
            'rol'      => User::ROL_BASICO,   // todo registro nuevo entra como usuario básico
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
