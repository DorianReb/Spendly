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

        /* Appbar mini */
        .cats-topbar{
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            margin-bottom:.35rem;
        }
        .cats-title{ font-weight:800; letter-spacing:.02em; }
        .btn-clip{ border-radius:999px; border:0; background:color-mix(in oklab, var(--morado) 15%, transparent);
            color:var(--morado); font-weight:800; padding:.35rem .7rem;
        }

        /* Tabs principales */
        .tabs-cats{
            display:flex; gap:1.5rem; border-bottom:1px solid color-mix(in oklab, var(--text) 10%, transparent);
            margin:.3rem 0 1rem; padding-inline:.25rem;
        }
        .tabs-cats .tab{
            position:relative; padding:.65rem .2rem; text-decoration:none; color: color-mix(in oklab, var(--text) 65%, transparent);
            font-weight:900; letter-spacing:.02em;
        }
        .tabs-cats .tab.active{ color: var(--text); }
        .tabs-cats .tab.active::after{
            content:""; position:absolute; left:0; right:0; bottom:-1px; height:3px; background:var(--morado); border-radius:2px;
        }

        /* Grid de categorías (círculos + texto) */
        .cat-grid{
            --size:86px;
            display:grid; gap:1rem;
            grid-template-columns: repeat(3, minmax(0,1fr));
        }
        @media(min-width:480px){ .cat-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); } }
        @media(min-width:992px){ .cat-grid{ grid-template-columns: repeat(6, minmax(0,1fr)); } }

        .cat-item{ display:grid; justify-items:center; gap:.5rem; text-align:center; cursor:pointer; }
        .cat-icon{
            width:var(--size); height:var(--size); border-radius:999px; display:grid; place-items:center;
            box-shadow: 0 8px 18px rgba(0,0,0,.12);
            position:relative; isolation:isolate;
        }
        /* Icono en línea blanca */
        .cat-icon i{
            color:#fff; font-size: calc(var(--size) * .38); font-weight:400;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.25));
        }
        /* Aro interior sutil para look “línea” */
        .cat-icon::after{
            content:""; position:absolute; inset:8px; border-radius:999px; border:2px solid rgba(255,255,255,.75);
            pointer-events:none; mix-blend:screen;
        }
        .cat-name{ font-weight:700; color:var(--text); font-size:.95rem; line-height:1.1; }

        /* Botón Crear */
        .cat-create .cat-icon{ background:var(--amarillo); color:#2f2a18; }
        .cat-create i{ color:#2f2a18; }

        /* Tarjeta contenedora para sombra suave como tu Home */
        .panel{
            background: var(--card); border-radius: 1rem; padding: 1rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }

        html[data-theme="dark"] .panel{ background:#141414; }
        html[data-theme="dark"] .cat-icon::after{ border-color:rgba(255,255,255,.55); }
        html[data-theme="dark"] .tabs-cats{ border-bottom-color: color-mix(in oklab, #fff 12%, transparent); }
    </style>

    <div class="cats-wrap">
        {{-- Topbar --}}
        <div class="cats-topbar">
            <button class="btn btn-sm btn-outline-dark" onclick="history.back()">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </button>
            <div class="cats-title">Categorías</div>
            <button class="btn-clip"><i class="fa-regular fa-copy me-1"></i> Copiar</button>
        </div>

        {{-- Tabs --}}
        <nav class="tabs-cats" id="tabsCats">
            <a href="#" class="tab active" data-tab="gastos">GASTOS</a>
            <a href="#" class="tab" data-tab="ingresos">INGRESOS</a>
        </nav>

        {{-- Contenido --}}
        <section class="panel">
            <div class="cat-grid" id="catGrid">
                {{-- Se llena por JS con los datos de PHP --}}
            </div>
        </section>
    </div>

    @php
        // Si no recibimos datos desde el controlador, usa un “seed” de ejemplo.
        $demoGastos = [
            ['nombre'=>'Salud',        'color'=>'#ef476f', 'icon'=>'fa-heart-pulse'],
            ['nombre'=>'Ocio',         'color'=>'#20c997', 'icon'=>'fa-champagne-glasses'],
            ['nombre'=>'Casa',         'color'=>'#ffa94d', 'icon'=>'fa-house'],
            ['nombre'=>'Café',         'color'=>'#9775fa', 'icon'=>'fa-mug-saucer'],
            ['nombre'=>'Educación',    'color'=>'#4dabf7', 'icon'=>'fa-graduation-cap'],
            ['nombre'=>'Regalos',      'color'=>'#e599f7', 'icon'=>'fa-gift'],
            ['nombre'=>'Alimentación', 'color'=>'#69db7c', 'icon'=>'fa-basket-shopping'],
            ['nombre'=>'Familia',      'color'=>'#f4a62a', 'icon'=>'fa-people-roof'],
            ['nombre'=>'Rutina',       'color'=>'#ff922b', 'icon'=>'fa-dumbbell'],
            ['nombre'=>'Transporte',   'color'=>'#6ea8fe', 'icon'=>'fa-bus'],
            ['nombre'=>'Otros',        'color'=>'#adb5bd', 'icon'=>'fa-circle-question'],
        ];
        $demoIngresos = [
            ['nombre'=>'Salario',  'color'=>'#51cf66', 'icon'=>'fa-briefcase'],
            ['nombre'=>'Freelance','color'=>'#94d82d', 'icon'=>'fa-laptop-code'],
            ['nombre'=>'Intereses','color'=>'#a9e34b', 'icon'=>'fa-coins'],
            ['nombre'=>'Regalos',  'color'=>'#e599f7', 'icon'=>'fa-gift'],
            ['nombre'=>'Ventas',   'color'=>'#4dabf7', 'icon'=>'fa-hand-holding-dollar'],
        ];
    @endphp

    <script>
        // Datos desde PHP (controlador) o demo
        const DATA = {
            gastos  : @json($categoriasGastos ?? $demoGastos),
            ingresos: @json($categoriasIngresos ?? $demoIngresos),
        };

        const grid = document.getElementById('catGrid');
        const tabs = document.querySelectorAll('#tabsCats .tab');

        function renderCats(tipo='gastos'){
            grid.innerHTML = '';
            (DATA[tipo] || []).forEach(c => {
                const item = document.createElement('a');
                item.className = 'cat-item';
                item.href = "{{ route('movimientos.index') }}" + `?categoria=${encodeURIComponent(c.nombre)}&tipo=${tipo}`;
                item.setAttribute('title', c.nombre);

                item.innerHTML = `
                <div class="cat-icon" style="background:${c.color}">
                    <i class="fa-solid ${c.icon}"></i>
                </div>
                <div class="cat-name">${c.nombre}</div>
            `;
                grid.appendChild(item);
            });

            // Botón “Crear”
            const crear = document.createElement('a');
            crear.className = 'cat-item cat-create';
            crear.href = "{{ route('categorias.create') }}?tipo=${tipo}";
            crear.innerHTML = `
            <div class="cat-icon"><i class="fa-solid fa-plus"></i></div>
            <div class="cat-name">Crear</div>
        `;
            grid.appendChild(crear);
        }

        tabs.forEach(t=>{
            t.addEventListener('click', e=>{
                e.preventDefault();
                tabs.forEach(x=>x.classList.remove('active'));
                t.classList.add('active');
                renderCats(t.dataset.tab);
            });
        });

        // Primer render (GASTOS)
        renderCats('gastos');
    </script>
@endsection
