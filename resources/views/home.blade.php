@extends('layouts.app')
@section('title','Inicio — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#777;
            --radius:1.2rem;
        }
        .home-wrap{ max-width: 1100px; margin-inline:auto; }

        /* Top summary */
        .topbar-mini{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:.75rem;
        }
        .total{
            display:flex; flex-direction:column; align-items:center; justify-content:center;
        }
        .total .label{ color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .total .amount{ font-size: clamp(1.4rem,1.1rem + 2vw,2.2rem); font-weight:800; line-height:1; }

        .pill{ background: color-mix(in oklab, var(--morado) 18%, transparent); color: var(--morado);
            padding:.35em .7em; border-radius:999px; font-weight:700; }

        /* Tabs: Gastos/Ingresos/Metas */
        .tabs{
            display:flex; gap:1.25rem; align-items:center;
            border-bottom:1px solid color-mix(in oklab, var(--text) 12%, transparent);
            margin-bottom:.75rem; padding-inline:.25rem;
        }
        .tab{
            position:relative; padding:.75rem .25rem; color: color-mix(in oklab, var(--text) 70%, transparent);
            text-decoration:none; font-weight:800; letter-spacing:.02em;
        }
        .tab.active{ color: var(--text); }
        .tab.active::after{
            content:""; position:absolute; left:0; right:0; bottom:-1px; height:3px;
            background: var(--morado); border-radius:2px;
        }

        /* Sub filtros */
        .subtabs{ display:flex; gap:1rem; margin-bottom:.75rem; }
        .subtabs a{
            color: inherit; text-decoration:none; opacity:.75; font-weight:700;
            padding:.25rem .2rem; border-bottom:2px solid transparent;
        }
        .subtabs a.active{ color: var(--morado); opacity:1; border-color: var(--morado); }

        /* Card principal */
        .panel{
            background: var(--card); border-radius: 1rem; padding: 1rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }
        .panel-header{ display:flex; align-items:center; justify-content:space-between; margin-bottom: .75rem; }
        .panel-header .range a{ color: inherit; }
        .panel-body{ display:grid; grid-template-columns: 1fr; gap:1rem; }
        @media(min-width: 768px){ .panel-body{ grid-template-columns: 380px 1fr; align-items:center; } }

        /* Donut */
        .donut-wrap{ display:grid; place-items:center; padding: .5rem; }
        .donut-label{
            position:absolute; text-align:center; color:#fff; font-weight:800;
            font-size: clamp(1.1rem, 1rem + .8vw, 1.6rem);
            text-shadow: 0 2px 12px rgba(0,0,0,.35);
        }
        .donut{
            width: min(70vw, 320px); aspect-ratio:1/1; position:relative;
            border-radius:50%;
            background:
                radial-gradient(circle at center, #333 0 38%, transparent 38%),
                conic-gradient(#ff4b4b var(--percent, 65%), #242424 0%);
            box-shadow: inset 0 0 0 10px #3a3a3a, 0 8px 22px rgba(0,0,0,.3);
        }
        html[data-theme="dark"] .donut{
            background:
                radial-gradient(circle at center, #2b2b2b 0 38%, transparent 38%),
                conic-gradient(#ff4b4b var(--percent, 65%), #1a1a1a 0%);
            box-shadow: inset 0 0 0 10px #454545, 0 8px 22px rgba(0,0,0,.35);
        }

        /* Lista */
        .list{ display:flex; flex-direction:column; gap:.6rem; }
        .item{
            background: color-mix(in oklab, var(--text) 6%, transparent);
            border-radius:.9rem; padding:.8rem .9rem; display:flex; align-items:center; gap:.75rem;
        }
        html[data-theme="dark"] .item{ background: color-mix(in oklab, var(--text) 12%, transparent); }
        .item .icon{
            width:36px; height:36px; border-radius:50%; display:grid; place-items:center;
            color:#fff; background:#ff6b6b; flex-shrink:0;
        }
        .item .name{ font-weight:700; }
        .item .grow{ margin-left:auto; display:flex; align-items:center; gap:1rem; color: var(--muted); }
        .item-amount{ color: var(--text); font-weight:800; }

        /* FAB */
        .fab{
            width:52px; height:52px; border-radius:50%;
            background: var(--amarillo); color:#3a3220; border:none; font-weight:900;
            display:grid; place-items:center; box-shadow: 0 12px 24px rgba(0,0,0,.18);
        }
        .fab:hover{ filter: brightness(.95); }

        /* Util */
        .muted{ color: var(--muted); }
    </style>

    <div class="home-wrap">
        {{-- Top Summary --}}
        <div class="topbar-mini">
            <div class="pill"><i class="fa-solid fa-sack-dollar me-1"></i> Total</div>
            <div class="total">
                <div class="amount">{{ $totalFormatted ?? '$500.00' }}</div>
            </div>
            <button class="btn btn-sm btn-outline-dark"><i class="fa-regular fa-bookmark me-1"></i> Guardar</button>
        </div>

        {{-- Tabs: Gastos / Ingresos / Metas --}}
        <div class="tabs" id="tabs">
            <a href="#" class="tab active" data-tab="gastos">GASTOS</a>
            <a href="#" class="tab" data-tab="ingresos">INGRESOS</a>
            <a href="#" class="tab" data-tab="metas">METAS</a>
        </div>

        {{-- Sub-filtros --}}
        <div class="subtabs" id="subtabs">
            <a href="#" class="active" data-range="dia">Día</a>
            <a href="#" data-range="semana">Semana</a>
            <a href="#" data-range="mes">Mes</a>
            <a href="#" data-range="anio">Año</a>
            <a href="#" data-range="periodo">Período</a>
        </div>

        {{-- Panel principal --}}
        <section class="panel">
            <div class="panel-header">
                <div class="range">
                    <small class="muted"><span id="rangeLabel">6 oct – 12 oct</span></small>
                </div>
                <button class="fab" title="Agregar"><i class="fa-solid fa-plus"></i></button>
            </div>

            <div class="panel-body">
                {{-- Donut --}}
                <div class="donut-wrap">
                    <div class="donut" id="donut" style="--percent: {{ $porcentaje ?? 100 }}%;"></div>
                    <div class="donut-label" id="donutLabel">{{ $montoCentro ?? '$500' }}</div>
                </div>

                {{-- Lista --}}
                <div class="list" id="lista">
                    {{-- Ejemplo de item (GASTOS) --}}
                    <div class="item">
                        <div class="icon" style="background:#ff6b6b"><i class="fa-solid fa-heart-pulse"></i></div>
                        <div class="name">Salud</div>
                        <div class="grow">
                            <span>100 %</span>
                            <span class="item-amount">$500</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // --- Tabs principales ---
        const tabs = document.querySelectorAll('.tab');
        const lista = document.getElementById('lista');
        const donut = document.getElementById('donut');
        const donutLabel = document.getElementById('donutLabel');

        const datasets = {
            gastos: {
                percent: 80,
                center: '$2,450',
                items: [
                    {icon:'fa-utensils', color:'#ff6b6b', name:'Comida', pct:'45 %', amount:'$1,100'},
                    {icon:'fa-bus', color:'#ffa94d', name:'Transporte', pct:'25 %', amount:'$600'},
                    {icon:'fa-house', color:'#748ffc', name:'Renta', pct:'30 %', amount:'$750'},
                ]
            },
            ingresos: {
                percent: 60,
                center: '$8,000',
                items: [
                    {icon:'fa-briefcase', color:'#51cf66', name:'Salario', pct:'85 %', amount:'$6,800'},
                    {icon:'fa-hand-holding-dollar', color:'#94d82d', name:'Extra', pct:'15 %', amount:'$1,200'},
                ]
            },
            metas: {
                percent: 42,
                center: '42 %',
                items: [
                    {icon:'fa-piggy-bank', color:'#6C63FF', name:'Fondo de emergencia', pct:'65 %', amount:'$6,500 / $10,000'},
                    {icon:'fa-plane', color:'#FFD460', name:'Viaje', pct:'30 %', amount:'$3,000 / $10,000'},
                ]
            }
        };

        function renderList(data){
            lista.innerHTML = '';
            data.items.forEach(it=>{
                const row = document.createElement('div');
                row.className = 'item';
                row.innerHTML = `
        <div class="icon" style="background:${it.color}"><i class="fa-solid ${it.icon}"></i></div>
        <div class="name">${it.name}</div>
        <div class="grow"><span>${it.pct}</span><span class="item-amount">${it.amount}</span><i class="fa-solid fa-chevron-down"></i></div>
      `;
                lista.appendChild(row);
            });
        }

        tabs.forEach(t=>{
            t.addEventListener('click', (e)=>{
                e.preventDefault();
                tabs.forEach(x=>x.classList.remove('active'));
                t.classList.add('active');
                const key = t.dataset.tab;
                const data = datasets[key];
                donut.style.setProperty('--percent', data.percent + '%');
                donutLabel.textContent = data.center;
                renderList(data);
            });
        });

        // Render inicial (gastos)
        renderList(datasets.gastos);

        // Subtabs demo (solo marca activo)
        document.querySelectorAll('.subtabs a').forEach(a=>{
            a.addEventListener('click', (e)=>{
                e.preventDefault();
                document.querySelectorAll('.subtabs a').forEach(x=>x.classList.remove('active'));
                a.classList.add('active');
                // Aquí podrías disparar fetch/axios para recargar datos por rango
            });
        });
    </script>
@endsection
