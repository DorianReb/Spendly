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
        .total .label{
            color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em;
        }
        .total .amount{
            font-size: clamp(1.4rem,1.1rem + 2vw,2.2rem); font-weight:800; line-height:1;
        }

        .pill{
            background: color-mix(in oklab, var(--morado) 18%, transparent);
            color: var(--morado);
            padding:.35em .7em; border-radius:999px; font-weight:700;
        }

        /* Tabs */
        .tabs{
            display:flex; gap:1.25rem; align-items:center;
            border-bottom:1px solid color-mix(in oklab, var(--text) 12%, transparent);
            margin-bottom:.75rem; padding-inline:.25rem;
        }
        .tab{
            position:relative; padding:.75rem .25rem;
            color: color-mix(in oklab, var(--text) 70%, transparent);
            text-decoration:none; font-weight:800; letter-spacing:.02em;
        }
        .tab.active{ color: var(--text); }
        .tab.active::after{
            content:""; position:absolute; left:0; right:0; bottom:-1px; height:3px;
            background: var(--morado); border-radius:2px;
        }

        /* Sub filtros */
        .subtabs{ display:flex; gap:1rem; margin-bottom:.75rem; flex-wrap:wrap; }
        .subtabs a{
            color: inherit; text-decoration:none; opacity:.75; font-weight:700;
            padding:.25rem .2rem; border-bottom:2px solid transparent;
        }
        .subtabs a.active{ color: var(--morado); opacity:1; border-color: var(--morado); }

        /* Form periodo */
        .periodo-form{
            display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:.75rem; align-items:center;
        }
        .periodo-form input{
            max-width:150px;
        }

        /* Card principal */
        .panel{
            background: var(--card); border-radius: 1rem; padding: 1rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }
        .panel-header{
            display:flex; align-items:center; justify-content:space-between;
            margin-bottom: .75rem;
            gap:.75rem;
        }
        .panel-header-range{
            display:flex; align-items:center; gap:.5rem; flex-grow:1;
        }
        .btn-nav-range{
            border:none; background:transparent; color:var(--muted);
            padding:0; line-height:1; width:32px; height:32px;
            display:inline-flex; align-items:center; justify-content:center;
            border-radius:999px;
        }
        .btn-nav-range:disabled{
            opacity:.35; pointer-events:none;
        }

        .panel-body{ display:grid; grid-template-columns: 1fr; gap:1rem; }
        @media(min-width: 768px){
            .panel-body{ grid-template-columns: 380px 1fr; align-items:center; }
        }

        /* Donut */
        .donut-wrap{ display:grid; place-items:center; padding: .5rem; position:relative; }
        .donut-label{
            position:absolute; text-align:center; color:#fff; font-weight:800;
            font-size: clamp(1.1rem, 1rem + .8vw, 1.6rem);
            text-shadow: 0 2px 12px rgba(0,0,0,.35);
        }
        .donut{
            width: min(70vw, 320px); aspect-ratio:1/1; position:relative;
            border-radius:50%;
            background:
                radial-gradient(circle at center, #333 0, #333 38%, transparent 38%),
                var(--donut-gradient, conic-gradient(#ff4b4b 0 100%));
            box-shadow: inset 0 0 0 10px #3a3a3a, 0 8px 22px rgba(0,0,0,.3);
        }
        .donut-empty{
            background:
                radial-gradient(circle at center, #ffffff 0, #ffffff 65%, transparent 65%);
            box-shadow: inset 0 0 0 10px #e5e5e5, 0 0 0 rgba(0,0,0,0);
        }

        html[data-theme="dark"] .donut{
            background:
                radial-gradient(circle at center, #2b2b2b 0, #2b2b2b 38%, transparent 38%),
                var(--donut-gradient, conic-gradient(#ff4b4b 0 100%));
            box-shadow: inset 0 0 0 10px #454545, 0 8px 22px rgba(0,0,0,.35);
        }
        html[data-theme="dark"] .donut-empty{
            background:
                radial-gradient(circle at center, #2b2b2b 0, #2b2b2b 65%, transparent 65%);
            box-shadow: inset 0 0 0 10px #555, 0 0 0 rgba(0,0,0,0);
        }

        /* Lista */
        .list{ display:flex; flex-direction:column; gap:.6rem; }
        .item{
            background: color-mix(in oklab, var(--text) 6%, transparent);
            border-radius:.9rem; padding:.8rem .9rem;
            display:flex; align-items:center; gap:.75rem;
            cursor:pointer;
        }
        html[data-theme="dark"] .item{
            background: color-mix(in oklab, var(--text) 12%, transparent);
        }
        .item .icon{
            width:36px; height:36px; border-radius:50%;
            display:grid; place-items:center;
            color:#fff; background:#ff6b6b; flex-shrink:0;
        }
        .item .name{ font-weight:700; }
        .item .grow{
            margin-left:auto; display:flex; align-items:center;
            gap:1rem; color: var(--muted);
        }
        .item-amount{ color: var(--text); font-weight:800; }

        /* FAB */
        .fab{
            width:52px; height:52px; border-radius:50%;
            background: var(--amarillo); color:#3a3220; border:none; font-weight:900;
            display:grid; place-items:center; box-shadow: 0 12px 24px rgba(0,0,0,.18);
        }
        .fab:hover{ filter: brightness(.95); }

        .muted{ color: var(--muted); }
    </style>

    @php
        $scope   = $scope   ?? 'dia';
        $tipoTab = $tipoTab ?? 'gasto';

        $currentKey  = $tipoTab === 'ingreso' ? 'ingresos' : 'gastos';
        $currentData = $datasets[$currentKey] ?? ['center' => '$0.00', 'items' => [], 'total' => 0];

        $items = $currentData['items'] ?? [];
        $acc   = 0;
        $stops = [];

        foreach ($items as $it) {
            $pct = isset($it['pct']) ? floatval($it['pct']) : 0;
            if ($pct <= 0) continue;

            $start = $acc;
            $end   = $acc + $pct;
            $color = $it['color'] ?? '#ff4b4b';

            $stops[] = "{$color} {$start}% {$end}%";
            $acc = $end;
        }

        $hasItems = !empty($stops);
        $donutGradient = $hasItems
            ? 'conic-gradient('.implode(', ', $stops).')'
            : 'conic-gradient(#e0e0e0 0 100%)';
    @endphp

    <div class="home-wrap">
        {{-- Top Summary --}}
        <div class="topbar-mini">
            <div class="pill">
                <i class="fa-solid fa-scale-balanced me-1"></i> Balance
            </div>
            <div class="total">
                <div class="label">TOTAL HISTÓRICO</div>
                <div class="amount">{{ $balanceFormatted }}</div>
            </div>
            <button class="btn btn-sm btn-outline-dark">
                <i class="fa-regular fa-bookmark me-1"></i> Guardar
            </button>
        </div>

        {{-- Tabs --}}
        <div class="tabs">
            <a href="{{ route('home', ['tipo' => 'gasto', 'scope' => $scope]) }}"
               class="tab {{ $tipoTab === 'gasto' ? 'active' : '' }}">
                GASTOS
            </a>
            <a href="{{ route('home', ['tipo' => 'ingreso', 'scope' => $scope]) }}"
               class="tab {{ $tipoTab === 'ingreso' ? 'active' : '' }}">
                INGRESOS
            </a>
            <a href="#" class="tab" onclick="alert('Módulo de metas próximamente'); return false;">
                METAS
            </a>
        </div>

        {{-- Sub-filtros --}}
        <div class="subtabs">
            @foreach (['dia' => 'Día','semana'=>'Semana','mes'=>'Mes','anio'=>'Año','periodo'=>'Período'] as $key => $label)
                <a href="{{ route('home', ['tipo' => $tipoTab, 'scope' => $key]) }}"
                   class="{{ $scope === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Formulario Período --}}
        @if($scope === 'periodo')
            <form method="GET" action="{{ route('home') }}" class="periodo-form">
                <input type="hidden" name="tipo"  value="{{ $tipoTab }}">
                <input type="hidden" name="scope" value="periodo">

                <label class="small mb-0 me-1">Desde:</label>
                <input type="text" id="desde" name="desde"
                       class="form-control form-control-sm"
                       value="{{ $desdePeriodo }}">

                <label class="small mb-0 ms-2 me-1">Hasta:</label>
                <input type="text" id="hasta" name="hasta"
                       class="form-control form-control-sm"
                       value="{{ $hastaPeriodo }}">

                <button type="submit" class="btn btn-sm btn-primary ms-2">
                    Aplicar
                </button>
            </form>
        @endif

        {{-- Panel principal --}}
        <section class="panel">
            <div class="panel-header">
                <div class="panel-header-range">
                    <button class="btn-nav-range" type="button"
                            @if($prevUrl) onclick="window.location.href='{{ $prevUrl }}'" @else disabled @endif>
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <small class="muted flex-grow-1 text-center">
                        <span id="rangeLabel">{{ $rangeLabel }}</span>
                    </small>

                    <button class="btn-nav-range" type="button"
                            @if($nextUrl) onclick="window.location.href='{{ $nextUrl }}'" @else disabled @endif>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <button id="fabAdd" class="fab" type="button" title="Agregar">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <div class="panel-body">
                {{-- Donut --}}
                <div class="donut-wrap">
                    <div class="donut {{ $hasItems ? '' : 'donut-empty' }}"
                         id="donut"
                         style="--donut-gradient: {{ $donutGradient }};">
                    </div>
                    <div class="donut-label" id="donutLabel">
                        {{ $currentData['center'] ?? '$0.00' }}
                    </div>
                </div>

                {{-- Lista --}}
                <div class="list" id="lista"></div>
            </div>
        </section>
    </div>

    {{-- Flatpickr CSS/JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const lista         = document.getElementById('lista');
        const fabAdd        = document.getElementById('fabAdd');
        const baseCreateUrl = "{{ route('transacciones.create') }}";

        const currentData = @json($currentData);
        const currentTipo = "{{ $tipoTab === 'ingreso' ? 'ingreso' : 'gasto' }}";

        function renderList(data){
            lista.innerHTML = '';
            if (!data || !data.items) return;

            data.items.forEach(it => {
                const row = document.createElement('div');
                row.className = 'item';
                row.innerHTML = `
                    <div class="icon" style="background:${it.color}">
                        <i class="fa-solid ${it.icon}"></i>
                    </div>
                    <div class="name">${it.name}</div>
                    <div class="grow">
                        <span>${it.pct}%</span>
                        <span class="item-amount">${it.amount}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                `;

                row.addEventListener('click', () => {
                    const url = "{{ url('transacciones/categoria') }}/"
                        + it.categoria_id + '?tipo=' + currentTipo;
                    window.location.href = url;
                });

                lista.appendChild(row);
            });
        }

        renderList(currentData);

        if (fabAdd) {
            fabAdd.addEventListener('click', () => {
                window.location.href = baseCreateUrl + '?tipo=' + currentTipo;
            });
        }

        // Flatpickr para Período
        if ("{{ $scope }}" === 'periodo' && window.flatpickr) {
            flatpickr('#desde', { dateFormat: 'Y-m-d' });
            flatpickr('#hasta', { dateFormat: 'Y-m-d' });
        }
    </script>
@endsection
