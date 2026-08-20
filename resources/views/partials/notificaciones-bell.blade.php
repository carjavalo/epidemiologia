{{-- Campana de notificaciones (barra superior) — diseño moderno --}}
<li class="nav-item dropdown pa-noti">
    <a class="nav-link" data-toggle="dropdown" href="#" title="Notificaciones">
        <i class="far fa-bell"></i>
        @if(($notiNoLeidas ?? 0) > 0)
            <span class="pa-noti-badge">{{ $notiNoLeidas > 99 ? '99+' : $notiNoLeidas }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-right pa-noti-menu">
        {{-- Cabecera --}}
        <div class="pa-noti-head">
            <div class="pa-noti-head-titulo">
                <i class="far fa-bell mr-1"></i> Notificaciones
                @if(($notiNoLeidas ?? 0) > 0)
                    <span class="pa-noti-count">{{ $notiNoLeidas }}</span>
                @endif
            </div>
            @if(($notiUltimas ?? collect())->isNotEmpty())
                <form action="{{ route('notificaciones.vaciar') }}" method="POST" class="m-0"
                      onsubmit="return confirm('¿Vaciar todas las notificaciones?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="pa-noti-vaciar" title="Vaciar todas">
                        <i class="fas fa-trash-alt mr-1"></i>Vaciar
                    </button>
                </form>
            @endif
        </div>

        {{-- Lista --}}
        <div class="pa-noti-lista">
            @forelse(($notiUltimas ?? collect()) as $n)
                <div class="pa-noti-item {{ $n->leida ? '' : 'no-leida' }}">
                    <a href="{{ route('notificaciones.leer', $n->id) }}" class="pa-noti-link">
                        <span class="pa-noti-ico {{ $n->color }}"><i class="fas {{ $n->icono }}"></i></span>
                        <span class="pa-noti-body">
                            <span class="pa-noti-titulo">{{ $n->titulo }}</span>
                            <span class="pa-noti-msg">{{ \Illuminate\Support\Str::limit($n->mensaje, 100) }}</span>
                            <span class="pa-noti-time"><i class="far fa-clock mr-1"></i>{{ $n->created_at?->diffForHumans() }}</span>
                        </span>
                    </a>
                    <form action="{{ route('notificaciones.eliminar', $n->id) }}" method="POST" class="pa-noti-del">
                        @csrf @method('DELETE')
                        <button type="submit" title="Eliminar"><i class="fas fa-times"></i></button>
                    </form>
                </div>
            @empty
                <div class="pa-noti-vacio">
                    <i class="far fa-bell-slash"></i>
                    <span>No hay notificaciones</span>
                </div>
            @endforelse
        </div>

        {{-- Pie --}}
        @if(($notiNoLeidas ?? 0) > 0)
            <form action="{{ route('notificaciones.leerTodas') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="pa-noti-pie">
                    <i class="fas fa-check-double mr-1"></i> Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>
</li>
