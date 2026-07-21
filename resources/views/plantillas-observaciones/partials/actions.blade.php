<div class="btn-group" role="group">
    <a href="{{ route('plantillas-observaciones.show', $plantilla->id) }}" 
       class="btn btn-info btn-sm" 
       title="Ver detalles">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ route('plantillas-observaciones.edit', $plantilla->id) }}" 
       class="btn btn-warning btn-sm" 
       title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    
    <form action="{{ route('plantillas-observaciones.destroy', $plantilla->id) }}" 
          method="POST" 
          style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="button"
                class="btn btn-danger btn-sm btn-delete"
                data-plantilla-name="ID: {{ $plantilla->id }}"
                title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
