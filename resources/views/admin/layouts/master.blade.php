@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="/css/custom.css">
    @yield('extra_css')
@stop
{{-- El menú de usuario duplicado se eliminó: se usa el usermenu nativo de AdminLTE (uno solo, en todas las páginas). --}}