<div class="btn-group" role="group">
    <a href="{{ route('pantimicrobiano.show', $pantimicrobiano) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('pantimicrobiano.edit', $pantimicrobiano) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('pantimicrobiano.destroy', $pantimicrobiano) }}"
            data-descripcion="{{ $pantimicrobiano->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
