{{--
    Aviso de términos nuevos sin estandarizar para un catálogo.

    Se incluye en la portada de los tres CRUD (servicios, tipos de muestra y
    sitios). Cuando una importación trae un texto que no coincide con ningún
    valor del catálogo, el dato entra tal cual y aparece aquí, con un enlace
    para vincularlo a un término existente.

    Uso:  @include('partials.aviso-pendientes', ['catalogo' => 'servicio'])
--}}
@php
    $pendientesCatalogo = \App\Support\Estandarizador::pendientes($catalogo);
    $registrosAfectados = array_sum($pendientesCatalogo);
@endphp

@if(count($pendientesCatalogo) > 0)
    <div class="r-aviso r-aviso--adv" style="align-items:center;">
        <i class="fas fa-triangle-exclamation"></i>
        <span>
            <b>{{ count($pendientesCatalogo) }}
                {{ count($pendientesCatalogo) === 1 ? 'término nuevo sin estandarizar' : 'términos nuevos sin estandarizar' }}</b>
            Llegaron en una importación y no coinciden con ningún valor de este catálogo, así que
            entraron tal cual. Afectan a {{ number_format($registrosAfectados) }}
            {{ $registrosAfectados === 1 ? 'registro' : 'registros' }}.
            @if(count($pendientesCatalogo) <= 3)
                ({{ implode(', ', array_slice(array_keys($pendientesCatalogo), 0, 3)) }})
            @endif
        </span>
        <a href="{{ route('equivalencias.pendientes', ['catalogo' => $catalogo]) }}"
           class="r-btn" style="margin-left:auto;flex:0 0 auto;white-space:nowrap;">
            <i class="fas fa-link mr-1"></i> Vincular a un término existente
        </a>
    </div>
@endif
