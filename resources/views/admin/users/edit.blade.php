@extends('admin.layouts.master')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Modificar Usuario: {{ $user->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuario">Usuario</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                                            id="usuario" name="usuario" value="{{ old('usuario', $user->usuario ?? $user->name) }}" placeholder="Nombre de usuario" required>
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
                                            <option value="basico" {{ old('rol', $user->rol) == 'basico' ? 'selected' : '' }}>Usuario básico</option>
                                            <option value="administrador" {{ old('rol', $user->rol) == 'administrador' ? 'selected' : '' }}>Administrador</option>
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
                                            id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="correo@ejemplo.com" required>
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

                                    @if($user->hasProfileImage())
                                        <div class="mb-3">
                                            <div class="text-center">
                                                <img src="{{ $user->profile_image_url }}" alt="Foto actual"
                                                     class="img-thumbnail rounded-circle"
                                                     style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                            </div>
                                            <div class="mt-2 text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                    <label class="form-check-label text-danger" for="remove_image">
                                                        <i class="fas fa-trash"></i> Eliminar foto actual
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-3 text-center">
                                            <div class="avatar-placeholder" style="width: 150px; height: 150px; margin: 0 auto;">
                                                <img src="{{ $user->profile_image_url }}" alt="Avatar por defecto"
                                                     class="img-thumbnail rounded-circle"
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <small class="text-muted">Avatar generado automáticamente</small>
                                        </div>
                                    @endif

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('foto') is-invalid @enderror"
                                                id="foto" name="foto" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                            <label class="custom-file-label" for="foto">
                                                {{ $user->hasProfileImage() ? 'Cambiar foto...' : 'Seleccionar foto...' }}
                                            </label>
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
                                    <!-- Preview de la nueva imagen -->
                                    <div class="mt-3 text-center" id="image-preview" style="display: none;">
                                        <div class="mb-2">
                                            <strong class="text-info"><i class="fas fa-eye"></i> Nueva foto:</strong>
                                        </div>
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
                                            <small class="text-muted">Vista previa - Esta imagen reemplazará la actual</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña <small class="text-muted">(Dejar en blanco para mantener la actual)</small></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                            id="password" name="password" placeholder="Mínimo 8 caracteres">
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
                                            id="password_confirmation" name="password_confirmation" placeholder="Confirmar contraseña">
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
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar Usuario
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
        console.log('Formulario de edición de usuario cargado!');

        // Función para preview de imagen
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validar tamaño del archivo (2MB máximo)
                if (file.size > 2048000) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 2MB.');
                    input.value = '';
                    $(input).next('.custom-file-label').html('{{ $user->hasProfileImage() ? "Cambiar foto..." : "Seleccionar foto..." }}');
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Solo se permiten archivos JPG, JPEG, PNG y GIF.');
                    input.value = '';
                    $(input).next('.custom-file-label').html('{{ $user->hasProfileImage() ? "Cambiar foto..." : "Seleccionar foto..." }}');
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
            $('#foto').next('.custom-file-label').html('{{ $user->hasProfileImage() ? "Cambiar foto..." : "Seleccionar foto..." }}');
        }

        // Actualizar label del input file cuando se selecciona archivo
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName || '{{ $user->hasProfileImage() ? "Cambiar foto..." : "Seleccionar foto..." }}');
        });

        // Manejar checkbox de eliminar imagen
        $('#remove_image').on('change', function() {
            if ($(this).is(':checked')) {
                $('#profile_image').prop('disabled', true);
                $('.custom-file-label').addClass('disabled').html('Imagen será eliminada');
            } else {
                $('#profile_image').prop('disabled', false);
                $('.custom-file-label').removeClass('disabled').html('{{ $user->hasProfileImage() ? "Cambiar imagen..." : "Seleccionar imagen..." }}');
            }
        });
    </script>
@stop