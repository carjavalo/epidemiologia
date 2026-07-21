@extends('admin.layouts.master')

@section('title', 'Detalle de Usuario')

@section('content_header')
    <h1>Detalle de Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Información de {{ $user->name }} {{ $user->apellido1 }} {{ $user->apellido2 }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 25%" class="bg-light">ID</th>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Nombre</th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Primer Apellido</th>
                                            <td>{{ $user->apellido1 }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Segundo Apellido</th>
                                            <td>{{ $user->apellido2 }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Correo Electrónico</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Verificado</th>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Verificado el 
                                                        {{ \Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Pendiente de verificación
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Fecha de Registro</th>
                                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Foto de Perfil</th>
                                            <td>
                                                @if($user->hasProfileImage())
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-camera"></i> Foto personalizada
                                                    </span>
                                                    <br><small class="text-muted">
                                                        Archivo: {{ $user->foto ?: $user->profile_image }}
                                                    </small>
                                                @else
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-user-circle"></i> Avatar generado automáticamente
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light">Última Actualización</th>
                                            <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="user-profile">
                                <div class="profile-image-container mb-3" style="position: relative; display: inline-block;">
                                    <img src="{{ $user->profile_image_url }}"
                                         alt="Foto de {{ $user->full_name }}"
                                         class="img-circle img-fluid border p-2 profile-image"
                                         style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                         onclick="showImageModal('{{ $user->profile_image_url }}', '{{ $user->full_name }}')"
                                         title="Click para ampliar">

                                    @if($user->hasProfileImage())
                                        <span class="badge badge-success profile-badge"
                                              style="position: absolute; top: 10px; right: 10px; font-size: 0.8em;"
                                              title="Foto personalizada">
                                            <i class="fas fa-camera"></i> Personalizada
                                        </span>
                                    @else
                                        <span class="badge badge-info profile-badge"
                                              style="position: absolute; top: 10px; right: 10px; font-size: 0.8em;"
                                              title="Avatar generado automáticamente">
                                            <i class="fas fa-user-circle"></i> Generado
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mb-2">{{ $user->full_name }}</h3>
                                <p class="text-muted mb-1">{{ $user->email }}</p>

                                @if($user->hasProfileImage())
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> Con foto personalizada
                                    </small>
                                @else
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i> Avatar automático
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group" role="group">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger delete-btn">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ampliar imagen -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Foto de Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto de perfil" class="img-fluid rounded" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra_css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .user-profile .profile-image {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 3px solid #fff !important;
        }
        .user-profile .profile-image:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        .profile-badge {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .profile-image-container {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Confirmación para eliminar
            $('.delete-form').submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Función para mostrar imagen en modal
        function showImageModal(imageUrl, userName) {
            $('#modalImage').attr('src', imageUrl);
            $('#modalImage').attr('alt', 'Foto de ' + userName);
            $('#imageModalLabel').text('Foto de Perfil - ' + userName);
            $('#imageModal').modal('show');
        }
    </script>
@stop 