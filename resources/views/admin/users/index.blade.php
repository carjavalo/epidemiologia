@extends('admin.layouts.master')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">Listado de usuarios del sistema</h3>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="users-table" class="table table-striped table-hover table-bordered responsive nowrap" width="100%">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Email</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center user-info-cell" data-user-id="{{ $user->id }}">
                                        <div class="position-relative photo-container">
                                            <img src="{{ $user->small_avatar }}"
                                                 class="img-circle elevation-1 mr-2 {{ $user->hasProfileImage() ? 'real-profile-photo' : 'generated-avatar' }}"
                                                 style="width: 40px; height: 40px; object-fit: cover; transition: all 0.2s ease-in-out; cursor: pointer;"
                                                 title="{{ $user->hasProfileImage() ? 'Foto de perfil personalizada - Click para ampliar' : 'Avatar generado automáticamente' }}"
                                                 alt="Foto de {{ $user->full_name }}"
                                                 data-user-name="{{ $user->full_name }}"
                                                 data-has-custom-image="{{ $user->hasProfileImage() ? 'true' : 'false' }}"
                                                 onclick="showImageModal('{{ $user->profile_image_url }}', '{{ $user->full_name }}')">
                                            @if($user->hasProfileImage())
                                                <span class="photo-badge"
                                                      style="position: absolute; top: -3px; right: 5px; width: 14px; height: 14px;
                                                             background: #28a745; border: 2px solid #fff; border-radius: 50%;
                                                             box-shadow: 0 1px 3px rgba(0,0,0,0.3);"
                                                      title="Foto personalizada">
                                                </span>
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">
                                                <strong>{{ $user->usuario ?? $user->name }}</strong>
                                                @if($user->hasProfileImage())
                                                    <i class="fas fa-camera text-success ml-1" style="font-size: 10px;" title="Tiene foto personalizada"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->esAdmin())
                                        <span class="badge badge-danger"><i class="fas fa-user-shield"></i> Administrador</span>
                                    @else
                                        <span class="badge badge-secondary">Usuario básico</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete-btn" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen completa -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Imagen de Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra_css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Estilos para fotos de perfil reales */
        .real-profile-photo {
            border: 2px solid #28a745 !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2) !important;
        }

        .real-profile-photo:hover {
            transform: scale(1.05);
            border-color: #20c997 !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3) !important;
            cursor: pointer;
        }

        /* Estilos para avatares generados */
        .generated-avatar {
            border: 2px solid #6c757d !important;
            opacity: 0.9;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2) !important;
        }

        .generated-avatar:hover {
            transform: scale(1.02);
            border-color: #5a6268 !important;
            opacity: 1;
            cursor: pointer;
        }

        /* Contenedor de foto con efectos */
        .photo-container {
            transition: all 0.2s ease-in-out;
        }

        .photo-container:hover {
            filter: brightness(1.05);
        }

        /* Badge indicador de foto personalizada */
        .photo-badge {
            animation: pulse-success 2s infinite;
        }

        @keyframes pulse-success {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 4px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        /* Estilos para información del usuario */
        .user-info-cell {
            min-height: 50px;
            align-items: center;
        }

        .user-details {
            flex: 1;
            min-width: 0; /* Para permitir text-overflow */
        }

        .user-name {
            font-weight: 500;
            color: #495057;
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }

        .user-surnames {
            line-height: 1.2;
            margin-bottom: 1px;
        }

        /* Efectos de hover para toda la celda */
        .user-info-cell:hover .user-name {
            color: #007bff;
        }

        .user-info-cell:hover .real-profile-photo {
            border-color: #007bff !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3) !important;
        }

        /* Asegurar visibilidad de imágenes */
        .img-circle {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        .img-circle[src] {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        /* Indicador de carga solo cuando sea necesario */
        .img-circle.loading {
            background: linear-gradient(90deg, #f8f9fa 25%, #e9ecef 50%, #f8f9fa 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-info-cell {
                min-height: 45px;
            }

            .photo-container img {
                width: 35px !important;
                height: 35px !important;
            }

            .photo-badge {
                width: 12px !important;
                height: 12px !important;
                top: -2px !important;
                right: 3px !important;
            }

            .user-name {
                font-size: 14px;
            }

            .user-surnames {
                font-size: 11px;
            }
        }

        /* Animación de entrada para las filas */
        .dataTables_wrapper tbody tr {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                responsive: true,
                autoWidth: false,
                dom: '<"row"<"col-sm-12 col-md-4"B><"col-sm-12 col-md-4"p><"col-sm-12 col-md-4"f>>rt<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copiar',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-columns"></i> Columnas',
                        className: 'btn btn-primary'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                stateSave: true,
                ordering: true,
                fixedHeader: true,
                scrollCollapse: true,
                columnDefs: [
                    { targets: 0, visible: false }, // Ocultar columna ID
                    { responsivePriority: 1, targets: 1 }, // Usuario
                    { responsivePriority: 2, targets: 2 }, // Rol
                    { responsivePriority: 3, targets: 3 }, // Email
                    { responsivePriority: 4, targets: 5 }, // Acciones
                    { width: '120px', targets: 5 } // Ancho para columna Acciones
                ]
            });

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

            // Ajustar tabla cuando cambia el tamaño de la ventana
            $(window).on('resize', function() {
                $('#users-table').DataTable().columns.adjust().responsive.recalc();
            });
        });

        // Función para mostrar imagen en modal
        function showImageModal(imageUrl, userName) {
            $('#modalImage').attr('src', imageUrl);
            $('#modalImage').attr('alt', userName);
            $('#imageModalLabel').text('Imagen de Perfil - ' + userName);
            $('#imageModal').modal('show');
        }

        // Función simplificada para manejar imágenes
        function setupImageHandling() {
            const images = document.querySelectorAll('.user-info-cell img');

            images.forEach(img => {
                // Asegurar que todas las imágenes sean visibles
                img.style.opacity = '1';
                img.style.visibility = 'visible';

                const userName = img.getAttribute('data-user-name');
                const hasCustomImage = img.getAttribute('data-has-custom-image') === 'true';

                // Solo configurar manejo de errores para imágenes personalizadas
                if (hasCustomImage) {
                    img.onerror = function() {
                        console.log('Error cargando imagen personalizada para:', userName);

                        // Generar avatar de respaldo
                        const fallbackUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=6c757d&color=fff&size=40&rounded=true&bold=true`;

                        this.src = fallbackUrl;
                        this.className = 'img-circle elevation-1 mr-2 generated-avatar error-fallback';
                        this.title = 'Avatar generado automáticamente';
                        this.onerror = null; // Evitar bucles infinitos

                        // Remover indicadores de foto personalizada
                        const badge = this.parentElement.querySelector('.photo-badge');
                        if (badge) badge.remove();

                        const cameraIcon = this.parentElement.parentElement.querySelector('.fa-camera');
                        if (cameraIcon) cameraIcon.remove();
                    };
                }
            });
        }

        // Función para verificar y cargar imágenes correctamente
        function optimizeImageLoading() {
            // Configurar manejo de imágenes
            setupImageHandling();

            const images = document.querySelectorAll('.user-info-cell img');

            images.forEach(img => {
                // Asegurar visibilidad inmediata
                img.style.opacity = '1';
                img.style.visibility = 'visible';
                img.style.display = 'inline-block';

                // Marcar como procesada
                img.setAttribute('data-loaded', 'true');
            });
        }

        // Función para verificar el estado de las imágenes
        function checkImageStatus() {
            const images = document.querySelectorAll('.user-info-cell img');
            images.forEach(img => {
                if (img.src.includes('ui-avatars.com')) {
                    console.log('Avatar generado para:', img.alt);
                } else if (img.src.includes('storage/profile_images')) {
                    console.log('Imagen personalizada:', img.src, 'Estado:', img.complete ? 'Cargada' : 'Cargando');
                }
            });
        }

        // Función para agregar efectos de hover mejorados
        function addHoverEffects() {
            $(document).on('mouseenter', '.user-info-cell', function() {
                const $cell = $(this);
                const $photo = $cell.find('.photo-container img');
                const $badge = $cell.find('.photo-badge');

                // Efecto de hover en la foto
                $photo.addClass('hover-effect');

                // Animación del badge si existe
                if ($badge.length) {
                    $badge.css('animation', 'pulse-success 1s infinite');
                }

                // Efecto en el texto
                $cell.find('.user-name').addClass('text-primary');
            });

            $(document).on('mouseleave', '.user-info-cell', function() {
                const $cell = $(this);
                const $photo = $cell.find('.photo-container img');
                const $badge = $cell.find('.photo-badge');

                $photo.removeClass('hover-effect');
                $badge.css('animation', 'pulse-success 2s infinite');
                $cell.find('.user-name').removeClass('text-primary');
            });
        }

        // Inicializar optimizaciones cuando la tabla esté lista
        $(document).ready(function() {
            // Inicializar optimizaciones después de que DataTable se cargue
            setTimeout(() => {
                optimizeImageLoading();
                addHoverEffects();

                // Verificar estado de imágenes después de la carga
                setTimeout(() => {
                    checkImageStatus();
                }, 1000);
            }, 500);
        });
    </script>
@stop