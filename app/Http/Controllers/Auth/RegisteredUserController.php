<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        \Log::info('=== INICIO REGISTRO ===');
        \Log::info('Datos recibidos: ', $request->all());
        
        $request->validate([
            'usuario' => ['required', 'string', 'max:100', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(6)],
        ]);

        \Log::info('Validación OK, creando usuario...');

        $user = User::create([
            'usuario' => $request->usuario,
            'name' => $request->usuario, // se mantiene para compatibilidad (avatar/nombre AdminLTE)
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => User::ROL_BASICO, // todo registro nuevo entra como usuario básico
        ]);

        \Log::info('Usuario creado: ID=' . $user->id);

        event(new Registered($user));

        Auth::login($user);
        
        \Log::info('Login exitoso, redirigiendo a dashboard');

        return redirect(route('dashboard', absolute: false));
    }
}
