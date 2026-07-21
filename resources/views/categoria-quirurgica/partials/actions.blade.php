<div class="btn-group" role="group">
    <a href="{{ route('categoria-quirurgica.show', $categoriaQuirurgica) }}"
       class="btn btn-info btn-sm"
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('categoria-quirurgica.edit', $categoriaQuirurgica) }}"
       class="btn btn-warning btn-sm"
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button"
            class="btn btn-danger btn-sm btn-delete"
            data-url="{{ route('categoria-quirurgica.destroy', $categoriaQuirurgica) }}"
            data-descripcion="{{ $categoriaQuirurgica->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
