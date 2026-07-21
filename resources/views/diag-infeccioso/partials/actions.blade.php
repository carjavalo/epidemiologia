<div class="btn-group" role="group">
    <a href="{{ route('diag-infeccioso.show', $diagInfeccioso) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('diag-infeccioso.edit', $diagInfeccioso) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('diag-infeccioso.destroy', $diagInfeccioso) }}"
            data-descripcion="{{ $diagInfeccioso->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
