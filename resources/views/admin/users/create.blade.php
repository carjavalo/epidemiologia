@extends('admin.layouts.master')

@section('title', 'Crear Usuario')

@section('content')
    <div class="uf-head">
        <h1 class="uf-title"><i class="fas fa-user-plus" style="color:#2a377e;"></i> Crear usuario</h1>
        <p class="uf-sub">Registra un nuevo usuario del sistema y define su rol de acceso.</p>
    </div>

    <div class="uf-card">
        <div class="uf-card-head">
            <span class="uf-ico"><i class="fas fa-id-card"></i></span>
            <div>
                <h2>Datos del usuario</h2>
                <p>Los campos marcados son obligatorios.</p>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="uf-body">
                <div class="uf-grid">
                    <div class="uf-group">
                        <label class="uf-label" for="usuario">Usuario</label>
                        <div class="uf-field">
                            <i class="fas fa-user uf-lead"></i>
                            <input type="text" class="uf-input @error('usuario') is-invalid @enderror"
                                   id="usuario" name="usuario" value="{{ old('usuario') }}"
                                   placeholder="Nombre de usuario" required>
                        </div>
                        @error('usuario') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="uf-group">
                        <label class="uf-label" for="rol">Rol</label>
                        <div class="uf-field">
                            <i class="fas fa-user-shield uf-lead"></i>
                            <select class="uf-input @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                                <option value="basico" {{ old('rol', 'basico') == 'basico' ? 'selected' : '' }}>Usuario básico</option>
                                <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        @error('rol') <span class="uf-error">{{ $message }}</span> @enderror
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
                                    <option value="{{ $val }}" {{ old('perfil') == $val ? 'selected' : '' }}>{{ $label }}</option>
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
                                   id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                        </div>
                        @error('email') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="uf-grid">
                    <div class="uf-group">
                        <label class="uf-label" for="password">Contraseña</label>
                        <div class="uf-field">
                            <i class="fas fa-lock uf-lead"></i>
                            <input type="password" class="uf-input @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        @error('password') <span class="uf-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="uf-group">
                        <label class="uf-label" for="password_confirmation">Confirmar contraseña</label>
                        <div class="uf-field">
                            <i class="fas fa-lock uf-lead"></i>
                            <input type="password" class="uf-input"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Repite la contraseña" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="uf-foot">
                <a href="{{ route('users.index') }}" class="uf-btn uf-btn--ghost">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button type="submit" class="uf-btn uf-btn--primary">
                    <i class="fas fa-save"></i> Guardar usuario
                </button>
            </div>
        </form>
    </div>
@stop

@section('extra_css')
    @include('admin.users.estilos-form')
@stop
