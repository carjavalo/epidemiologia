@php $actual = old('catalogo', $equivalencia->catalogo ?? 'servicio'); @endphp

<div class="form-group">
    <label class="proa-label">Catálogo <span class="r-req">*</span></label>
    <select name="catalogo" id="js-catalogo" class="form-control" required>
        @foreach($catalogos as $clave => $nombre)
            <option value="{{ $clave }}" @selected($actual === $clave)>{{ $nombre }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label class="proa-label">Texto que llega de la fuente <span class="r-req">*</span></label>
    <input type="text" name="texto_crudo" class="form-control" maxlength="190" required
           value="{{ old('texto_crudo', $equivalencia->texto_crudo ?? '') }}"
           style="font-family:ui-monospace,Menlo,monospace;">
    <small class="form-text text-muted">
        Tal como aparece en el archivo. No importan mayúsculas, tildes, espacios de más ni que
        el número vaya pegado a la letra: &laquo;UCI2&raquo;, &laquo;uci 2&raquo; y
        &laquo;UCI  2&raquo; se reconocen igual. La puntuación sí separa palabras, así que
        &laquo;U.C.I 2&raquo; necesitaría su propia equivalencia.
    </small>
</div>

{{-- Una lista de valores por catálogo; solo se muestra y se envía la del catálogo elegido. --}}
@foreach($valores as $clave => $opciones)
    <div class="form-group js-valores" data-catalogo="{{ $clave }}">
        <label class="proa-label">Se estandariza como <span class="r-req">*</span></label>
        <select name="valor" class="form-control">
            <option value="">— Seleccionar —</option>
            @foreach($opciones as $opcion)
                <option value="{{ $opcion }}" @selected(old('valor', $equivalencia->valor ?? '') === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
    </div>
@endforeach

<script>
    (function () {
        var selector = document.getElementById('js-catalogo');
        function refrescar() {
            document.querySelectorAll('.js-valores').forEach(function (bloque) {
                var activo = bloque.dataset.catalogo === selector.value;
                bloque.style.display = activo ? '' : 'none';
                // Deshabilitado = no se envía, así no llegan dos campos "valor".
                bloque.querySelector('select').disabled = !activo;
            });
        }
        selector.addEventListener('change', refrescar);
        refrescar();
    })();
</script>
