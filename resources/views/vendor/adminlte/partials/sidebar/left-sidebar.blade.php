<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        {{-- User panel (optional) --}}
        @if(Auth::check())
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image">
                    <img src="{{ Auth::user()->adminlte_image() }}"
                         class="img-circle elevation-2"
                         alt="{{ Auth::user()->full_name }}"
                         style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                         onclick="showUserImageModal('{{ Auth::user()->profile_image_url }}', '{{ Auth::user()->full_name }}')"
                         title="Click para ver imagen completa">
                </div>
                <div class="info flex-grow-1">
                    <a href="#" class="d-block">{{ Auth::user()->usuario ?? Auth::user()->name }}</a>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="pa-logout mb-0 ml-1">
                    @csrf
                    <button type="submit" class="pa-logout-btn" title="Cerrar sesión">
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
            </div>
        @endif

        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
                {{-- Configured sidebar links --}}
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>

</aside>
