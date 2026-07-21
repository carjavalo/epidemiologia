@extends('adminlte::page')

@section('title', 'Importar seguimiento microbiológico')

@section('content_header')
    <h1>Importar seguimiento microbiológico</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Cargar archivos TXT</h3>
        </div>

        <form action="{{ route('seguimiento.importar.procesar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label>TXT Epidemiología (BD madre)</label>
                    <input type="file" name="archivo_epidemiologia" class="form-control" accept=".txt,.csv" required>
                </div>

                <div class="form-group">
                    <label>TXT PROA / procedimientos</label>
                    <input type="file" name="archivo_proa" class="form-control" accept=".txt,.csv,.sql">
                </div>

                @if(session('resultado_epidemiologia') || session('resultado_proa'))
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h4>Epidemiología</h4>
                                    <p>Procesados: {{ session('resultado_epidemiologia.procesados', 0) }}</p>
                                    <p>Pacientes creados: {{ session('resultado_epidemiologia.pacientesCreados', 0) }}</p>
                                    <p>Seguimientos creados: {{ session('resultado_epidemiologia.seguimientosCreados', 0) }}</p>
                                    <p>Omitidos (sin ID): {{ session('resultado_epidemiologia.omitidos', 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h4>PROA</h4>
                                    <p>Procesados: {{ session('resultado_proa.procesados', 0) }}</p>
                                    <p>Actualizados: {{ session('resultado_proa.actualizados', 0) }}</p>
                                    <p>Sin paciente: {{ session('resultado_proa.sinPaciente', 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-upload"></i> Procesar archivos
                </button>
            </div>
        </form>
    </div>
@stop
