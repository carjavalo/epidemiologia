<div class="btn-group" role="group">
    <a href="{{ route('microorganismo.show', $microorganismo) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('microorganismo.edit', $microorganismo) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('microorganismo.destroy', $microorganismo) }}"
            data-descripcion="{{ $microorganismo->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
