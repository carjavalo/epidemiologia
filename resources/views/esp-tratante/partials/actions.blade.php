<div class="btn-group" role="group">
    <a href="{{ route('esp-tratante.show', $espTratante) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('esp-tratante.edit', $espTratante) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('esp-tratante.destroy', $espTratante) }}"
            data-descripcion="{{ $espTratante->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
