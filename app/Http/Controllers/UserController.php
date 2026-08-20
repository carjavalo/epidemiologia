<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('id')->paginate(15);

        // Métricas globales (no dependen de la página actual).
        $totalUsuarios = User::count();
        $totalAdmins   = User::where('rol', User::ROL_ADMIN)->count();
        $totalBasicos  = $totalUsuarios - $totalAdmins;

        return view('admin.users.index', compact('users', 'totalUsuarios', 'totalAdmins', 'totalBasicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'usuario' => ['required', 'string', 'max:100', 'unique:users,usuario'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'rol' => ['required', 'in:basico,administrador'],
            'perfil' => ['nullable', Rule::in(array_keys(User::PERFILES))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'usuario' => $request->usuario,
            'name' => $request->usuario,
            'email' => $request->email ?: null,
            'rol' => $request->rol,
            'perfil' => $request->perfil ?: null,
            'password' => Hash::make($request->password),
        ];

        // Procesar imagen de perfil si se proporciona
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->handleImageUpload($request->file('foto'));
        }

        $user = User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'usuario' => ['required', 'string', 'max:100', Rule::unique('users', 'usuario')->ignore($user->id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'rol' => ['required', 'in:basico,administrador'],
            'perfil' => ['nullable', Rule::in(array_keys(User::PERFILES))],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'usuario' => $request->usuario,
            'name' => $request->usuario,
            'email' => $request->email ?: null,
            'rol' => $request->rol,
            'perfil' => $request->perfil ?: null,
            'puede_editar_epidemiologia' => $request->boolean('puede_editar_epidemiologia'),
            'puede_editar_proa' => $request->boolean('puede_editar_proa'),
        ];

        // Actualizar contraseña solo si se proporciona
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8', 'confirmed'],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        // Procesar imagen de perfil
        if ($request->hasFile('foto')) {
            // Eliminar imagen anterior si existe (tanto foto como profile_image)
            if ($user->foto) {
                $this->deleteImage($user->foto);
            } elseif ($user->profile_image) {
                $this->deleteImage($user->profile_image);
            }
            $data['foto'] = $this->handleImageUpload($request->file('foto'));
            $data['profile_image'] = null; // Limpiar el campo anterior
        } elseif ($request->has('remove_image') && $request->remove_image == '1') {
            // Eliminar imagen si se solicita
            if ($user->foto) {
                $this->deleteImage($user->foto);
                $data['foto'] = null;
            } elseif ($user->profile_image) {
                $this->deleteImage($user->profile_image);
                $data['profile_image'] = null;
            }
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Eliminar imagen de perfil si existe (tanto foto como profile_image)
        if ($user->foto) {
            $this->deleteImage($user->foto);
        } elseif ($user->profile_image) {
            $this->deleteImage($user->profile_image);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Manejar la carga de imagen de perfil
     */
    private function handleImageUpload($image)
    {
        // Generar nombre único para la imagen
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Crear el directorio si no existe
        $directory = storage_path('app/public/profile_images');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Guardar imagen en storage/app/public/profile_images
        $image->storeAs('public/profile_images', $imageName);

        return $imageName;
    }

    /**
     * Eliminar imagen de perfil
     */
    private function deleteImage($imageName)
    {
        $imagePath = 'public/profile_images/' . $imageName;
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
    }
}
