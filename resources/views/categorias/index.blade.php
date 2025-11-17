@extends('layouts.app')
@section('title','Categorías — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#8b8b8b;
            --radius:1.2rem;
        }
        .cats-wrap{ max-width:980px; margin-inline:auto; padding: .25rem .25rem 1rem; }

        .cats-topbar{
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            margin-bottom:.35rem;
        }
        .cats-title{ font-weight:800; letter-spacing:.02em; }

        .tabs-cats{
            display:flex; gap:1.5rem; border-bottom:1px solid color-mix(in oklab, var(--text) 10%, transparent);
            margin:.3rem 0 1rem; padding-inline:.25rem;
        }
        .tabs-cats .tab{
            position:relative; padding:.65rem .2rem; text-decoration:none;
            color: color-mix(in oklab, var(--text) 65%, transparent);
            font-weight:900; letter-spacing:.02em; cursor:pointer; border:none; background:transparent;
        }
        .tabs-cats .tab.active{ color: var(--text); }
        .tabs-cats .tab.active::after{
            content:""; position:absolute; left:0; right:0; bottom:-1px; height:3px;
            background:var(--morado); border-radius:2px;
        }

        .panel{
            background: var(--card); border-radius: 1rem; padding: 1rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }

        .cat-grid{
            --size:86px;
            display:grid; gap:1rem;
            grid-template-columns: repeat(3, minmax(0,1fr));
        }
        @media(min-width:480px){ .cat-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); } }
        @media(min-width:992px){ .cat-grid{ grid-template-columns: repeat(6, minmax(0,1fr)); } }

        .cat-item{
            display:grid; justify-items:center; gap:.5rem; text-align:center;
            cursor:pointer; text-decoration:none; color:inherit;

            /* 👇 reset de botón */
            background:transparent;
            border:none;
            padding:0;
            box-shadow:none;
            outline:none;
        }
        .cat-item:focus-visible{
            outline:2px solid color-mix(in oklab, var(--morado) 45%, transparent);
            outline-offset:4px;
        }

        .cat-icon{
            width:var(--size); height:var(--size); border-radius:999px;
            display:grid; place-items:center;
            box-shadow: 0 8px 18px rgba(0,0,0,.12);
            position:relative; isolation:isolate;
            color:#fff;
        }
        .cat-icon i{
            font-size:1.8rem;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }
        .cat-icon::after{
            content:""; position:absolute; inset:8px; border-radius:999px;
            border:2px solid rgba(255,255,255,.75);
            pointer-events:none; mix-blend:screen;
        }
        .cat-name{
            font-weight:700; color:var(--text); font-size:.95rem; line-height:1.1;
        }

        .cat-create .cat-icon{
            background:var(--amarillo); color:#2f2a18;
        }

        /* Estado seleccionado */
        .cat-item.selected .cat-icon{
            box-shadow:0 0 0 3px color-mix(in oklab, var(--morado) 40%, transparent),
            0 10px 24px rgba(0,0,0,.18);
            transform:translateY(-2px);
        }
        .cat-item.selected .cat-name{
            color:var(--morado);
        }

        /* Barra de acciones inferior */
        .cat-actions{
            margin-top:1rem;
            padding:.6rem .9rem;
            border-radius:.9rem;
            background: color-mix(in oklab, var(--card) 90%, var(--beige));
            border:1px solid color-mix(in oklab, var(--text) 10%, transparent);
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            font-size:.9rem;
        }
        .cat-actions-name{
            font-weight:700;
        }
        .cat-actions small{ color:var(--muted); }

        .cat-actions.d-none{ display:none !important; }
    </style>

    <div class="cats-wrap">

        {{-- Topbar --}}
        <div class="cats-topbar">
            <button class="btn btn-sm btn-outline-dark" onclick="history.back()">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </button>
            <div class="cats-title">Categorías</div>
            <span></span>
        </div>

        {{-- Tabs --}}
        <nav class="tabs-cats" id="tabsCats">
            <button type="button" class="tab active" data-tab="gastos">GASTOS</button>
            <button type="button" class="tab" data-tab="ingresos">INGRESOS</button>
        </nav>

        {{-- Contenido --}}
        <section class="panel">
            {{-- GASTOS --}}
            <div id="tab-gastos" class="cat-grid tab-pane active">
                @foreach(($categoriasGasto ?? collect()) as $cat)
                    <button type="button"
                            class="cat-item"
                            data-id="{{ $cat->id }}"
                            data-nombre="{{ $cat->nombre }}"
                            data-edit-url="{{ route('categorias.edit', $cat) }}"
                            data-delete-url="{{ route('categorias.destroy', $cat) }}">
                        <div class="cat-icon" style="background: {{ $cat->color_hex ?? '#6C63FF' }}">
                            <i class="fa-solid {{ $cat->icon ?? 'fa-circle-question' }}"></i>
                        </div>
                        <div class="cat-name">{{ $cat->nombre }}</div>
                    </button>
                @endforeach

                {{-- Crear --}}
                <a href="{{ route('categorias.create', ['tipo' => 'gasto']) }}"
                   class="cat-item cat-create">
                    <div class="cat-icon">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="cat-name">Crear</div>
                </a>
            </div>

            {{-- INGRESOS --}}
            <div id="tab-ingresos" class="cat-grid tab-pane" style="display:none;">
                @foreach(($categoriasIngreso ?? collect()) as $cat)
                    <button type="button"
                            class="cat-item"
                            data-id="{{ $cat->id }}"
                            data-nombre="{{ $cat->nombre }}"
                            data-edit-url="{{ route('categorias.edit', $cat) }}"
                            data-delete-url="{{ route('categorias.destroy', $cat) }}">
                        <div class="cat-icon" style="background: {{ $cat->color_hex ?? '#6C63FF' }}">
                            <i class="fa-solid {{ $cat->icon ?? 'fa-circle-question' }}"></i>
                        </div>
                        <div class="cat-name">{{ $cat->nombre }}</div>
                    </button>
                @endforeach

                {{-- Crear --}}
                <a href="{{ route('categorias.create', ['tipo' => 'ingreso']) }}"
                   class="cat-item cat-create">
                    <div class="cat-icon">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="cat-name">Crear</div>
                </a>
            </div>

            {{-- Barra de acciones: se llena por JS cuando se selecciona una categoría --}}
            <div id="catActions" class="cat-actions d-none">
                <div>
                    <div class="cat-actions-name" id="catActionsName">Categoría</div>
                    <small id="catActionsHint">Seleccionada</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" id="btnEditCat" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                    </a>
                    <button type="button" id="btnDeleteCat" class="btn btn-sm btn-outline-danger">
                        <i class="fa-solid fa-trash-can me-1"></i> Eliminar
                    </button>
                </div>
            </div>

            {{-- Formulario genérico para DELETE --}}
            <form id="deleteCatForm" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('#tabsCats .tab');
            const gastos = document.getElementById('tab-gastos');
            const ingresos = document.getElementById('tab-ingresos');

            const actionsBar   = document.getElementById('catActions');
            const actionsName  = document.getElementById('catActionsName');
            const actionsHint  = document.getElementById('catActionsHint');
            const btnEditCat   = document.getElementById('btnEditCat');
            const btnDeleteCat = document.getElementById('btnDeleteCat');
            const deleteForm   = document.getElementById('deleteCatForm');

            let selectedItem = null;
            let selectedEditUrl = null;
            let selectedDeleteUrl = null;

            // Cambio de pestañas
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    if (tab.dataset.tab === 'gastos') {
                        gastos.style.display = '';
                        ingresos.style.display = 'none';
                    } else {
                        gastos.style.display = 'none';
                        ingresos.style.display = '';
                    }

                    // al cambiar de tab, limpiar selección
                    clearSelection();
                });
            });

            // Selección de categoría
            function attachCatClickHandlers() {
                document.querySelectorAll('.cat-item[data-id]').forEach(item => {
                    item.addEventListener('click', () => {
                        // evitar seleccionar el botón "Crear"
                        if (!item.dataset.id) return;

                        // si vuelves a hacer click sobre la misma, deseleccionar
                        if (selectedItem === item) {
                            clearSelection();
                            return;
                        }

                        // limpiar selección anterior
                        document.querySelectorAll('.cat-item.selected').forEach(el => el.classList.remove('selected'));

                        selectedItem = item;
                        item.classList.add('selected');

                        const nombre   = item.dataset.nombre || 'Categoría';
                        selectedEditUrl   = item.dataset.editUrl || '#';
                        selectedDeleteUrl = item.dataset.deleteUrl || '#';

                        actionsName.textContent = nombre;
                        actionsHint.textContent = 'Seleccionada';

                        btnEditCat.href = selectedEditUrl;

                        actionsBar.classList.remove('d-none');
                    });
                });
            }

            function clearSelection() {
                selectedItem = null;
                selectedEditUrl = null;
                selectedDeleteUrl = null;
                document.querySelectorAll('.cat-item.selected').forEach(el => el.classList.remove('selected'));
                actionsBar.classList.add('d-none');
            }

            attachCatClickHandlers();

            // Botón eliminar
            btnDeleteCat.addEventListener('click', () => {
                if (!selectedDeleteUrl) return;

                Swal.fire({
                    title: '¿Eliminar categoría?',
                    text: 'Se eliminará la categoría seleccionada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.action = selectedDeleteUrl;
                        deleteForm.submit();
                    }
                });
            });



            // Si clicas fuera de la card, quitar selección (opcional)
            document.addEventListener('click', (e) => {
                const panel = document.querySelector('.panel');
                if (!panel.contains(e.target)) {
                    clearSelection();
                }
            });
        });
    </script>
@endsection
