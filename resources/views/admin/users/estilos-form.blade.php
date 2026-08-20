<style>
    /* ── Formularios de usuario: moderno, minimalista, shadowless ── */
    .uf-head { margin-bottom: 18px; }
    .uf-title { font-size: 1.55rem; font-weight: 700; color: #262b34; margin: 0; display:flex; align-items:center; gap:10px; }
    .uf-sub { margin: .3rem 0 0; color: #6b7280; font-size: .9rem; }

    .uf-card {
        background: #fff; border: 1px solid #e5e7f0; border-radius: 16px;
        box-shadow: none; overflow: hidden; max-width: 920px;
    }
    .uf-card-head { display: flex; align-items: center; gap: 14px; padding: 18px 22px; border-bottom: 1px solid #eef0f5; }
    .uf-ico {
        width: 42px; height: 42px; border-radius: 12px; flex: 0 0 auto;
        display: inline-flex; align-items: center; justify-content: center;
        background: #eef1fa; color: #2a377e; font-size: 1.1rem;
    }
    .uf-card-head h2 { font-size: 1.05rem; font-weight: 700; color: #262b34; margin: 0; }
    .uf-card-head p { margin: .12rem 0 0; font-size: .82rem; color: #6b7280; }
    .uf-body { padding: 22px; }

    .uf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .uf-grid { grid-template-columns: 1fr; } }

    .uf-group { margin-bottom: 16px; display: flex; flex-direction: column; }
    .uf-label { font-size: .82rem; font-weight: 600; color: #3b4250; margin-bottom: 6px; }
    .uf-label .opt { font-weight: 500; color: #9aa2b1; }

    .uf-field { position: relative; }
    .uf-field > i.uf-lead {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        color: #9aa2b1; font-size: .9rem; pointer-events: none;
    }
    .uf-input {
        width: 100%; border: 1px solid #dfe2ec; border-radius: 10px;
        padding: 10px 12px 10px 38px; font-size: .9rem; color: #374151; background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .uf-input:focus { outline: none; border-color: #2f6fed; box-shadow: 0 0 0 3px rgba(47,111,237,.12); }
    .uf-input.is-invalid { border-color: #e0574f; }
    select.uf-input { appearance: none; -webkit-appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%239aa2b1'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center; background-size: 14px; padding-right: 34px; }
    .uf-hint { font-size: .76rem; color: #9aa2b1; margin-top: 5px; }
    .uf-error { font-size: .78rem; color: #c0392b; margin-top: 5px; font-weight: 600; }

    /* Bloque de permisos */
    .uf-perms { border: 1px solid #e5e7f0; border-radius: 12px; padding: 14px 16px; background: #fbfcfe; margin-bottom: 16px; }
    .uf-perms-title { font-size: .82rem; font-weight: 700; color: #3b4250; margin: 0 0 4px; display: flex; align-items: center; gap: 7px; }
    .uf-perms-desc { font-size: .76rem; color: #9aa2b1; margin: 0 0 10px; }
    .uf-perms .custom-control { padding: 6px 0 6px 2.4rem; }
    .uf-perms .custom-control-label { font-size: .86rem; color: #454b56; padding-top: 1px; }
    .uf-perms .custom-control-input:checked ~ .custom-control-label::before { background: #2a377e; border-color: #2a377e; }
    .uf-perms .custom-control-input:focus ~ .custom-control-label::before { box-shadow: 0 0 0 3px rgba(47,111,237,.12); }

    /* Pie con acciones */
    .uf-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 22px; border-top: 1px solid #eef0f5; background: #fbfcfe; }
    .uf-btn { border: none; border-radius: 10px; padding: 9px 18px; font-size: .88rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px; text-decoration: none; transition: background-color .15s ease, color .15s ease; }
    .uf-btn--ghost { background: #fff; border: 1px solid #dfe2ec; color: #5a6172; }
    .uf-btn--ghost:hover { background: #f2f4fb; color: #2a377e; text-decoration: none; }
    .uf-btn--primary { background: #2a377e; color: #fff; }
    .uf-btn--primary:hover { background: #212a63; color: #fff; }
</style>
