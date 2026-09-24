<div class="form-group">
    <label for="nombre" class="proa-label">Nombre del sitio <span class="r-req">*</span></label>
    <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $sitio->nombre ?? '') }}" maxlength="150" required autofocus>
    @error('nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
    <small class="form-text text-muted">
        Tal como debe verse en toda la aplicación. Las variantes que llegan de la fuente se gestionan en Equivalencias.
    </small>
</div>
