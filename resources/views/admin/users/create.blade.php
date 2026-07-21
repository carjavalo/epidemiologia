@extends('admin.layouts.master')

@section('title', 'Crear Usuario')

@section('content_header')
    <h1>Crear Nuevo Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formulario de Registro</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuario">Usuario</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                                            id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Nombre de usuario" required>
                                        @error('usuario')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rol">Rol</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                        </div>
                                        <select class="form-control @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                                            <option value="basico" {{ old('rol', 'basico') == 'basico' ? 'selected' : '' }}>Usuario básico</option>
                                            <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                        </select>
                                        @error('rol')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo de imagen de perfil -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="foto">Foto de Perfil</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('foto') is-invalid @enderror"
                                                id="foto" name="foto" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                            <label class="custom-file-label" for="foto">Seleccionar foto...</label>
                                        </div>
                                        @error('foto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 2MB
                                    </small>
                                    <!-- Preview de la imagen -->
                                    <div class="mt-3 text-center" id="image-preview" style="display: none;">
                                        <div class="preview-container" style="display: inline-block; position: relative;">
                                            <img id="preview-img" src="" alt="Preview"
                                                 class="img-thumbnail rounded-circle"
                                                 style="width: 150px; height: 150px; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    style="position: absolute; top: -5px; right: -5px; border-radius: 50%; width: 30px; height: 30px; padding: 0;"
                                                    onclick="removePreview()" title="Quitar imagen">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Vista previa de la foto</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                            id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" 
                                            id="password_confirmation" name="password_confirmation" placeholder="Confirmar contraseña" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="float-left">
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra_css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Formulario de creación de usuario cargado!');

        // Función para preview de imagen
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validar tamaño del archivo (2MB máximo)
                if (file.size > 2048000) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                    input.value = '';
                    $(input).next('.custom-file-label').html('Seleccionar foto...');
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Solo se permiten archivos JPG, JPEG, PNG y GIF.');
                    input.value = '';
                    $(input).next('.custom-file-label').html('Seleccionar foto...');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-img').attr('src', e.target.result);
                    $('#image-preview').show();
                }
                reader.readAsDataURL(file);

                // Actualizar el label del input file
                var fileName = file.name;
                $(input).next('.custom-file-label').html(fileName);
            }
        }

        // Función para remover preview
        function removePreview() {
            $('#image-preview').hide();
            $('#foto').val('');
            $('#foto').next('.custom-file-label').html('Seleccionar foto...');
        }

        // Actualizar label del input file cuando se selecciona archivo
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Seleccionar foto...');
        });
    </script>
@stop