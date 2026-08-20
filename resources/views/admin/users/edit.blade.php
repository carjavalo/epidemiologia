@extends('admin.layouts.master')

@section('title', 'Editar Usuario')

@section('content')
    <div class="uf-head">
        <h1 class="uf-title"><i class="fas fa-user-edit" style="color:#2a377e;"></i> Editar usuario</h1>
        <p class="uf-sub">Modifica los datos, el rol y los permisos de <strong>{{ $user->usuario ?? $user->name }}</strong>.</p>
    </div>

    <div class="uf-card">
        <div class="uf-card-head">
            <span class="uf-ico"><i class="fas fa-id-card"></i></span>
            <div>
                <h2>Datos del usuario</h2>
                <p>Deja la contraseña en blanco para mantener la actual.</p>
            </div>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="uf-body">
                <div class="uf-grid">
                    <div class="uf-group">
                        <label class="uf-label" for="usuario">Usuario</label>
                        <div class="uf-field">
                            <i class="fas fa-user uf-lead"></i>
                            <input type="text" class="uf-input @error('usuario') is-invalid @enderror"
                                   id="usuario" name="usuario"
                                   value="{{ old('usuario', $user->usuario ?? $user->name) }}"
                                   placeholder="Nombre de usuario" required>
                        </div>
                        @error('usuario') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="uf-group">
                        <label class="uf-label" for="rol">Rol</label>
                        <div class="uf-field">
                            <i class="fas fa-user-shield uf-lead"></i>
                            <select class="uf-input @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                                <option value="basico" {{ old('rol', $user->rol) == 'basico' ? 'selected' : '' }}>Usuario básico</option>
                                <option value="administrador" {{ old('rol', $user->rol) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        @error('rol') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Permisos por área --}}
                <div class="uf-perms">
                    <p class="uf-perms-title"><i class="fas fa-user-lock" style="color:#2a377e;"></i> Permisos de edición</p>
                    <p class="uf-perms-desc">Controla si el usuario puede volver a modificar formularios ya registrados. Los administradores siempre pueden editar ambas áreas.</p>

                    <input type="hidden" name="puede_editar_epidemiologia" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="puede_editar_epidemiologia"
                               name="puede_editar_epidemiologia" value="1"
                               {{ old('puede_editar_epidemiologia', $user->puede_editar_epidemiologia) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="puede_editar_epidemiologia">
                            <i class="fas fa-vial mr-1 text-info"></i> Puede editar registros de <strong>Epidemiología</strong>
                        </label>
                    </div>

                    <input type="hidden" name="puede_editar_proa" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="puede_editar_proa"
                               name="puede_editar_proa" value="1"
                               {{ old('puede_editar_proa', $user->puede_editar_proa) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="puede_editar_proa">
                            <i class="fas fa-capsules mr-1 text-success"></i> Puede editar intervenciones <strong>PROA</strong>
                        </label>
                    </div>
                </div>

                <div class="uf-grid">
                    <div class="uf-group">
                        <label class="uf-label" for="perfil">Perfil / Cargo <span class="opt">(opcional)</span></label>
                        <div class="uf-field">
                            <i class="fas fa-user-tag uf-lead"></i>
                            <select class="uf-input @error('perfil') is-invalid @enderror" id="perfil" name="perfil">
                                <option value="">— Sin perfil —</option>
                                @foreach(\App\Models\User::PERFILES as $val => $label)
                                    <option value="{{ $val }}" {{ old('perfil', $user->perfil) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('perfil') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="uf-group">
                        <label class="uf-label" for="email">Correo electrónico <span class="opt">(opcional)</span></label>
                        <div class="uf-field">
                            <i class="fas fa-envelope uf-lead"></i>
                            <input type="email" class="uf-input @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="correo@ejemplo.com">
                        </div>
                        @error('email') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="uf-grid">
                    <div class="uf-group">
                        <label class="uf-label" for="password">Contraseña <span class="opt">(dejar en blanco para no cambiarla)</span></label>
                        <div class="uf-field">
                            <i class="fas fa-lock uf-lead"></i>
                            <input type="password" class="uf-input @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Mínimo 8 caracteres">
                        </div>
                        @error('password') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="uf-group">
                        <label class="uf-label" for="password_confirmation">Confirmar contraseña</label>
                        <div class="uf-field">
                            <i class="fas fa-lock uf-lead"></i>
                            <input type="password" class="uf-input"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Repite la contraseña">
                        </div>
                    </div>
                </div>
            </div>

            <div class="uf-foot">
                <a href="{{ route('users.index') }}" class="uf-btn uf-btn--ghost">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button type="submit" class="uf-btn uf-btn--primary">
                    <i class="fas fa-save"></i> Actualizar usuario
                </button>
            </div>
        </form>
    </div>
@stop

@section('extra_css')
    @include('admin.users.estilos-form')
@stop
