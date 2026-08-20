<div class="btn-group" role="group">
    <a href="{{ route('diagnosticos.show', $diagnostico) }}" class="btn btn-info btn-sm" title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('diagnosticos.edit', $diagnostico) }}" class="btn btn-warning btn-sm" title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-sm btn-delete"
            data-url="{{ route('diagnosticos.destroy', $diagnostico) }}"
            data-descripcion="{{ $diagnostico->codigo }} - {{ $diagnostico->descripcion }}"
            title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
