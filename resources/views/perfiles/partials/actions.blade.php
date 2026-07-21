<div class="btn-group" role="group">
    <a href="{{ route('perfiles.show', $perfil) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('perfiles.edit', $perfil) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" 
            class="btn btn-danger btn-sm btn-delete" 
            data-url="{{ route('perfiles.destroy', $perfil) }}" 
            data-id="{{ $perfil->id }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
