{{-- Mensajes de la sesión, con el mismo componente de aviso del formulario de registros. --}}
@if(session('success'))
    <div class="r-aviso r-aviso--ok">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="r-aviso r-aviso--err">
        <i class="fas fa-times-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif
@if($errors->any())
    <div class="r-aviso r-aviso--adv">
        <i class="fas fa-exclamation-circle"></i>
        <span>
            <b>Revisa los datos</b>
            {{ implode(' ', $errors->all()) }}
        </span>
    </div>
@endif
