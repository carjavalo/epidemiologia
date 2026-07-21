<div class="btn-group" role="group">
    <a href="{{ route('sis-internacional.show', $sistema->id) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('sis-internacional.edit', $sistema->id) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('sis-internacional.destroy', $sistema->id) }}"
            data-descripcion="{{ $sistema->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
