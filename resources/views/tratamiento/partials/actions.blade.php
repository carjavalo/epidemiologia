<div class="btn-group" role="group">
    <a href="{{ route('tratamiento.show', $tratamiento) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('tratamiento.edit', $tratamiento) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('tratamiento.destroy', $tratamiento) }}"
            data-descripcion="{{ $tratamiento->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
