@extends('admin.layouts.master')

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <div class="u-head">
        <div>
            <h1 class="u-title"><i class="fas fa-users mr-2"></i>Usuarios</h1>
            <p class="u-sub">Gestiona las cuentas, roles y permisos del sistema.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Nuevo usuario
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="u-flash u-flash--ok">
            <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Métricas --}}
    <div class="u-stats">
        <div class="u-stat">
            <span class="u-stat-num">{{ $totalUsuarios }}</span>
            <span class="u-stat-lbl">Usuarios en total</span>
        </div>
        <div class="u-stat">
            <span class="u-stat-num">{{ $totalAdmins }}</span>
            <span class="u-stat-lbl">Administradores</span>
        </div>
        <div class="u-stat">
            <span class="u-stat-num">{{ $totalBasicos }}</span>
            <span class="u-stat-lbl">Usuarios básicos</span>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="u-card">
        <div class="u-card-head">
            <span class="u-ico"><i class="fas fa-list-ul"></i></span>
            <div>
                <h3>Listado de usuarios</h3>
                <p class="u-card-sub">Rol, permisos por área y acceso.</p>
            </div>
            <div class="u-search">
                <i class="fas fa-search"></i>
                <input type="text" id="u-buscar" placeholder="Buscar usuario o correo…" autocomplete="off">
            </div>
        </div>

        <div class="u-tabla-wrap">
            <table class="u-tabla">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Permisos</th>
                        <th>Correo</th>
                        <th style="white-space:nowrap;">Registro</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="u-tbody">
                    @forelse($users as $user)
                        <tr class="u-row" data-buscar="{{ Str::lower(($user->usuario ?? $user->name) . ' ' . $user->email) }}">
                            <td>
                                <div class="u-usercell">
                                    <img src="{{ $user->small_avatar }}"
                                         class="u-avatar {{ $user->hasProfileImage() ? 'u-avatar--real' : '' }}"
                                         alt="Foto de {{ $user->full_name }}"
                                         title="{{ $user->hasProfileImage() ? 'Foto personalizada — clic para ampliar' : 'Avatar generado' }}"
                                         onclick="showImageModal('{{ $user->profile_image_url }}', '{{ $user->full_name }}')">
                                    <div>
                                        <strong>{{ $user->usuario ?? $user->name }}</strong>
                                        @if($user->hasProfileImage())
                                            <i class="fas fa-camera text-success ml-1" style="font-size:10px;" title="Tiene foto"></i>
                                        @endif
                                        <span class="u-id">#{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->esAdmin())
                                    <span class="u-badge u-badge--admin"><i class="fas fa-user-shield mr-1"></i>Administrador</span>
                                @else
                                    <span class="u-badge u-badge--basic">Usuario básico</span>
                                @endif
                            </td>
                            <td>
                                @if($user->esAdmin())
                                    <span class="u-chip u-chip--all">Acceso total</span>
                                @else
                                    @if($user->puede_editar_epidemiologia)
                                        <span class="u-chip u-chip--epi"><i class="fas fa-vial mr-1"></i>Epidemiología</span>
                                    @endif
                                    @if($user->puede_editar_proa)
                                        <span class="u-chip u-chip--proa"><i class="fas fa-capsules mr-1"></i>PROA</span>
                                    @endif
                                    @if(!$user->puede_editar_epidemiologia && !$user->puede_editar_proa)
                                        <span class="u-chip u-chip--none">Solo lectura</span>
                                    @endif
                                @endif
                            </td>
                            <td class="u-email">{{ $user->email ?: '—' }}</td>
                            <td class="u-fecha">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="u-vacio"><i class="fas fa-user-slash"></i><p>No hay usuarios registrados.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
            <div id="u-sin-resultados" class="u-vacio" style="display:none;">
                <i class="fas fa-search"></i><p>Ningún usuario coincide con la búsqueda.</p>
            </div>
        </div>

        @if($users->hasPages())
            <div class="u-pagina">
                <span class="u-pagina-info">
                    Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuarios
                </span>
                {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

    {{-- Modal ver imagen --}}
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Imagen de perfil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra_css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .u-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .u-title { font-size:1.6rem; font-weight:700; color:#262b34; margin:0; }
    .u-sub   { margin:.3rem 0 0; color:#6b7280; font-size:.9rem; }

    .u-flash {
        display:flex; align-items:center; gap:10px;
        background:#edf7f1; border:1px solid #cfe8db; color:#1f6146;
        border-radius:14px; padding:12px 18px; margin-bottom:16px; font-size:.9rem;
    }
    .u-flash i { font-size:1.05rem; }

    /* Métricas */
    .u-stats { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:14px; margin-bottom:18px; }
    @media (max-width: 575.98px){ .u-stats{ grid-template-columns:1fr; } }
    .u-stat {
        background:rgba(255,255,255,0.97); border:1px solid #e5e7f0; border-radius:14px;
        padding:16px 18px; box-shadow:none;
        border-top:3px solid #2a377e;
    }
    .u-stat-num { display:block; font-size:1.9rem; font-weight:700; color:#262b34; line-height:1; }
    .u-stat-lbl { display:block; margin-top:5px; font-size:.74rem; text-transform:uppercase; letter-spacing:.05em; color:#8b93a5; font-weight:600; }

    /* Tarjeta principal */
    .u-card {
        background:rgba(255,255,255,0.97); border:1px solid #e5e7f0; border-radius:16px;
        box-shadow:none; overflow:hidden;
    }
    .u-card-head { display:flex; align-items:center; gap:14px; padding:18px 22px; border-bottom:1px solid #eef0f5; flex-wrap:wrap; }
    .u-ico { width:38px; height:38px; border-radius:11px; display:inline-flex; align-items:center; justify-content:center; background:#eef1fa; color:#2a377e; font-size:1rem; flex:0 0 auto; }
    .u-card-head h3 { font-size:1rem; font-weight:700; color:#262b34; margin:0; }
    .u-card-sub { margin:.15rem 0 0; font-size:.82rem; color:#6b7280; }
    .u-search { margin-left:auto; position:relative; }
    .u-search i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9aa2b1; font-size:.85rem; }
    .u-search input {
        border:1px solid #e0e3ee; border-radius:10px; padding:8px 12px 8px 32px;
        font-size:.86rem; width:240px; max-width:60vw; background:#fff; color:#454b56;
    }
    .u-search input:focus { outline:none; border-color:#2f6fed; box-shadow:0 0 0 3px rgba(47,111,237,.12); }

    /* Tabla */
    .u-tabla-wrap { overflow-x:auto; }
    .u-tabla { width:100%; border-collapse:separate; border-spacing:0; }
    .u-tabla thead th {
        background:#f5f6fa; color:#5a6172; font-size:.72rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.05em; text-align:left;
        padding:11px 18px; border-bottom:1px solid #e5e7f0; white-space:nowrap;
    }
    .u-tabla tbody td { padding:12px 18px; border-bottom:1px solid #f2f3f8; vertical-align:middle; font-size:.87rem; color:#454b56; }
    .u-tabla tbody tr:last-child td { border-bottom:0; }
    .u-tabla tbody tr.u-row:hover { background:#f9fafd; }

    .u-usercell { display:flex; align-items:center; gap:10px; }
    .u-avatar { width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #dde3f5; cursor:pointer; transition:transform .15s ease, border-color .15s ease; }
    .u-avatar:hover { transform:scale(1.08); border-color:#2f6fed; }
    .u-avatar--real { border-color:#2e9e5b; }
    .u-usercell strong { color:#262b34; font-weight:600; }
    .u-id { color:#b3b9c5; font-size:.75rem; margin-left:4px; font-variant-numeric:tabular-nums; }

    .u-badge { display:inline-flex; align-items:center; font-size:.74rem; font-weight:600; padding:3px 10px; border-radius:999px; }
    .u-badge--admin { background:#eef1fa; color:#2a377e; }
    .u-badge--basic { background:#f2f3f6; color:#6b7280; }

    .u-chip { display:inline-flex; align-items:center; font-size:.72rem; font-weight:600; padding:3px 9px; border-radius:8px; margin:2px 4px 2px 0; }
    .u-chip--all  { background:#eef1fa; color:#2a377e; }
    .u-chip--epi  { background:#eaf0fb; color:#1f4fa0; }
    .u-chip--proa { background:#e9f3ee; color:#256a49; }
    .u-chip--none { background:#f4f5f7; color:#9aa2b1; }

    .u-email { color:#6b7280; }
    .u-fecha { color:#8b93a5; white-space:nowrap; font-variant-numeric:tabular-nums; }

    .u-vacio { text-align:center; padding:40px 20px; color:#8b93a5; }
    .u-vacio i { font-size:2rem; opacity:.45; }
    .u-vacio p { margin:10px 0 0; font-weight:600; color:#6b7280; }

    .modal-content { border:0; border-radius:16px; overflow:hidden; }
    .modal-header { background:#2a377e; color:#fff; border-bottom:0; }
    .modal-header .close { color:#fff; opacity:.85; text-shadow:none; }

    /* Paginación */
    .u-pagina { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:14px 22px 6px; }
    .u-pagina-info { font-size:.8rem; color:#8b93a5; }
    .u-pagina nav { margin-left:auto; }
    .u-pagina .pagination { display:flex; margin:0; gap:6px; flex-wrap:wrap; list-style:none; padding:0; }
    .u-pagina .page-item .page-link {
        border:1px solid #e5e7f0; border-radius:10px; color:#454b56; min-width:36px; height:36px;
        display:flex; align-items:center; justify-content:center; font-size:.85rem; font-weight:600;
        background:#fff; margin:0; transition:background-color .15s ease, color .15s ease, border-color .15s ease;
    }
    .u-pagina .page-item .page-link:hover { background:#eef1fa; color:#2a377e; border-color:#dbe1f3; }
    .u-pagina .page-item.active .page-link { background:#2a377e; border-color:#2a377e; color:#fff; box-shadow:none; }
    .u-pagina .page-item.disabled .page-link { color:#c3c9d4; background:#fff; border-color:#eef0f5; }
    .u-pagina .page-link:focus { outline:none; box-shadow:0 0 0 3px rgba(47,111,237,.15); }
</style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            // Búsqueda en vivo (cliente)
            $('#u-buscar').on('input', function () {
                var q = $(this).val().toLowerCase().trim();
                var visibles = 0;
                $('#u-tbody .u-row').each(function () {
                    var match = $(this).data('buscar').indexOf(q) !== -1;
                    $(this).toggle(match);
                    if (match) visibles++;
                });
                $('#u-sin-resultados').toggle(visibles === 0);
            });

            // Confirmación al eliminar
            $('.delete-form').on('submit', function (e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: '¿Eliminar usuario?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(function (r) { if (r.isConfirmed) form.submit(); });
            });
        });

        function showImageModal(imageUrl, userName) {
            $('#modalImage').attr('src', imageUrl).attr('alt', userName);
            $('#imageModalLabel').text('Imagen de perfil — ' + userName);
            $('#imageModal').modal('show');
        }
    </script>
@stop
