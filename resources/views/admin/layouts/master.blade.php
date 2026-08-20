@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="/css/custom.css">
    @yield('extra_css')
@stop

{{-- Campana de notificaciones en la barra superior (no duplica el avatar). --}}
@section('content_top_nav_right')
    @include('partials.notificaciones-bell')
@stop
{{-- El menú de usuario duplicado se eliminó: se usa el usermenu nativo de AdminLTE (uno solo, en todas las páginas). --}}