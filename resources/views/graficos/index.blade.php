@extends('layouts.app')
@section('title','Gráficos — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#777;
            --radius:1.2rem;
        }

        .grafico-wrap{
            max-width: 1100px;
            margin-inline:auto;
        }

        .topbar-mini{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
            margin-bottom:.75rem;
        }
        .pill{
            background: color-mix(in oklab, var(--morado) 18%, transparent);
            color: var(--morado);
            padding:.35em .7em;
            border-radius:999px;
            font-weight:700;
        }
        .range-label{
            font-size:.9rem;
            color:var(--muted);
            font-weight:600;
        }

        .btn-range-nav{
            border:none;
            background:transparent;
            color:var(--muted);
            width:32px;
            height:32px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:999px;
        }
        .btn-range-nav:disabled{
            opacity:.35;
            pointer-events:none;
        }

        /* Tabs tipo (general / gastos / ingresos) */
        .tabs{
            display:flex;
            gap:1.25rem;
            align-items:center;
            border-bottom:1px solid color-mix(in oklab, var(--text) 12%, transparent);
            margin-bottom:.75rem;
            padding-inline:.25rem;
        }
        .tab{
            position:relative;
            padding:.75rem .25rem;
            color: color-mix(in oklab, var(--text) 70%, transparent);
            text-decoration:none;
            font-weight:800;
            letter-spacing:.02em;
        }
        .tab.active{ color: var(--text); }
        .tab.active::after{
            content:"";
            position:absolute;
            left:0; right:0; bottom:-1px; height:3px;
            background: var(--morado);
            border-radius:2px;
        }

        /* Subfiltros: dia / semana / mes / anio */
        .subtabs{
            display:flex;
            gap:1rem;
            margin-bottom:.75rem;
            flex-wrap:wrap;
        }
        .subtabs a{
            color:inherit;
            text-decoration:none;
            opacity:.75;
            font-weight:700;
            padding:.25rem .2rem;
            border-bottom:2px solid transparent;
            font-size:.9rem;
        }
        .subtabs a.active{
            color: var(--morado);
            opacity:1;
            border-color: var(--morado);
        }

        /* Panel principal */
        .panel{
            background: var(--card);
            border-radius: 1rem;
            padding: 1rem 1.2rem 1.3rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
            margin-bottom:1rem;
        }
        html[data-theme="dark"] .panel{
            box-shadow: 0 10px 28px rgba(0,0,0,.4);
        }

        .panel-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
            margin-bottom:.75rem;
        }

        .summary-grid{
            display:grid;
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap:.75rem;
            margin-bottom:1rem;
        }
        @media(min-width: 768px){
            .summary-grid{
                grid-template-columns: repeat(4,minmax(0,1fr));
            }
        }

        .summary-card{
            border-radius:.9rem;
            padding:.6rem .75rem;
            background: color-mix(in oklab, var(--text) 4%, transparent);
            font-size:.85rem;
        }
        html[data-theme="dark"] .summary-card{
            background: color-mix(in oklab, var(--text) 14%, transparent);
        }
        .summary-label{
            color:var(--muted);
            font-weight:600;
            margin-bottom:.1rem;
        }
        .summary-value{
            font-weight:800;
            font-size:1rem;
        }

        .chart-wrap{
            padding:.4rem 0;
        }

        /* Lista de categorías */
        .cats-grid{
            display:grid;
            grid-template-columns: repeat(1,minmax(0,1fr));
            gap:.6rem;
        }
        @media(min-width: 768px){
            .cats-grid{
                grid-template-columns: repeat(2,minmax(0,1fr));
            }
        }

        .cat-item{
            border-radius:.9rem;
            padding:.7rem .8rem;
            display:flex;
            align-items:center;
            gap:.75rem;
            background: color-mix(in oklab, var(--text) 5%, transparent);
            text-decoration:none;
            color:inherit;
        }
        html[data-theme="dark"] .cat-item{
            background: color-mix(in oklab, var(--text) 16%, transparent);
        }
        .cat-icon{
            width:36px;
            height:36px;
            border-radius:50%;
            display:grid;
            place-items:center;
            color:#fff;
            flex-shrink:0;
        }
        .cat-name{
            font-weight:700;
        }
        .cat-amount{
            margin-left:auto;
            font-weight:800;
        }
    </style>

    @php
        $ingresos  = $dataset['ingresos']  ?? 0;
        $gastos    = $dataset['gastos']    ?? 0;
        $beneficio = $dataset['beneficio'] ?? 0;
        $perdida   = $dataset['perdida']   ?? 0;

        function fmoney($n){
            return '$' . number_format($n, 2);
        }
    @endphp

    <div class="grafico-wrap">
        {{-- Topbar / rango --}}
        <div class="topbar-mini">
            <div class="pill">
                <i class="fa-solid fa-chart-line me-1"></i> Gráficos
            </div>

            <div class="range-label">
                {{ $label }}
            </div>

            <div class="d-flex align-items-center gap-1">
                <button class="btn-range-nav"
                        @if($prevRef)
                            onclick="window.location.href='{{ route('grafico.index', ['scope'=>$scope,'tipo'=>$tipo,'ref'=>$prevRef]) }}'"
                        @else
                            disabled
                    @endif>
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <button class="btn-range-nav"
                        @if($nextRef)
                            onclick="window.location.href='{{ route('grafico.index', ['scope'=>$scope,'tipo'=>$tipo,'ref'=>$nextRef]) }}'"
                        @else
                            disabled
                    @endif>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- Tabs tipo --}}
        <div class="tabs">
            <a href="{{ route('grafico.index', ['scope'=>$scope,'tipo'=>'general']) }}"
               class="tab {{ $tipo === 'general' ? 'active' : '' }}">
                GENERAL
            </a>
            <a href="{{ route('grafico.index', ['scope'=>$scope,'tipo'=>'gastos']) }}"
               class="tab {{ $tipo === 'gastos' ? 'active' : '' }}">
                GASTOS
            </a>
            <a href="{{ route('grafico.index', ['scope'=>$scope,'tipo'=>'ingresos']) }}"
               class="tab {{ $tipo === 'ingresos' ? 'active' : '' }}">
                INGRESOS
            </a>
        </div>

        {{-- Subfiltros (rango) --}}
        <div class="subtabs">
            @foreach (['dia' => 'Día','semana'=>'Semana','mes'=>'Mes','anio'=>'Año'] as $key => $labelScope)
                <a href="{{ route('grafico.index', ['scope'=>$key,'tipo'=>$tipo]) }}"
                   class="{{ $scope === $key ? 'active' : '' }}">
                    {{ $labelScope }}
                </a>
            @endforeach
        </div>

        {{-- Panel principal --}}
        <section class="panel">
            {{-- Resumen numérico sólo en GENERAL --}}
            @if($tipo === 'general')
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Ingresos</div>
                        <div class="summary-value">{{ fmoney($ingresos) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Gastos</div>
                        <div class="summary-value">{{ fmoney($gastos) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Beneficio</div>
                        <div class="summary-value">{{ fmoney($beneficio) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Pérdida</div>
                        <div class="summary-value">{{ fmoney($perdida) }}</div>
                    </div>
                </div>
            @endif

            {{-- Gráfico --}}
            <div class="chart-wrap">
                <canvas id="mainChart" height="130"></canvas>
            </div>
        </section>

        {{-- Categorías del período --}}
        <section class="panel">
            <h6 class="mb-2">Categorías del período</h6>
            <div id="catsContainer">
                @if(empty($categorias))
                    <p class="text-muted small mb-0">
                        No hay transacciones en este rango de fechas.
                    </p>
                @else
                    <div class="cats-grid">
                        @foreach($categorias as $cat)
                            <a href="{{ $cat['url'] }}" class="cat-item">
                                <div class="cat-icon" style="background: {{ $cat['color'] }}">
                                    <i class="fa-solid {{ $cat['icon'] }}"></i>
                                </div>
                                <div class="cat-name">{{ $cat['nombre'] }}</div>
                                <div class="cat-amount">
                                    {{ fmoney($cat['total']) }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dataset      = @json($dataset);
            const tipoVista    = @json($tipo);              // 'general' | 'gastos' | 'ingresos'
            const catsGastos   = @json($catsGastos);
            const catsIngresos = @json($catsIngresos);
            const catsInicial  = @json($categorias);        // para tabs GASTOS / INGRESOS

            const canvas = document.getElementById('mainChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            function fmoney(n) {
                n = Number(n) || 0;
                return '$' + n.toFixed(2);
            }

            // Render dinámico de tarjetas de categorías
            function renderCategorias(lista) {
                const cont = document.getElementById('catsContainer');
                if (!cont) return;

                cont.innerHTML = '';

                if (!lista || !lista.length) {
                    cont.innerHTML = '<p class="text-muted small mb-0">No hay transacciones en este rango de fechas.</p>';
                    return;
                }

                const grid = document.createElement('div');
                grid.className = 'cats-grid';

                lista.forEach(cat => {
                    const link = document.createElement('a');
                    link.href = cat.url;
                    link.className = 'cat-item';

                    link.innerHTML = `
                        <div class="cat-icon" style="background:${cat.color}">
                            <i class="fa-solid ${cat.icon}"></i>
                        </div>
                        <div class="cat-name">${cat.nombre}</div>
                        <div class="cat-amount">
                            ${fmoney(cat.total)} <span class="text-muted small ms-2">${cat.pct ?? 0}%</span>
                        </div>
                    `;

                    grid.appendChild(link);
                });

                cont.appendChild(grid);
            }

            let chart;

            if (tipoVista === 'general') {
                // 4 barras con colores fijos
                const valores = [
                    dataset.ingresos  ?? 0,
                    dataset.gastos    ?? 0,
                    dataset.beneficio ?? 0,
                    dataset.perdida   ?? 0,
                ];

                const labels  = ['Ingresos','Gastos','Beneficio','Pérdida'];
                const colores = ['#6CBF8B','#E2B448','#5DA2E8','#E87060'];

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monto (MXN)',
                            data: valores,
                            backgroundColor: colores,
                            borderColor: colores,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const v = context.parsed.y || 0;
                                        return labels[context.dataIndex] + ': ' + fmoney(v);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: getComputedStyle(document.documentElement)
                                        .getPropertyValue('--text')
                                },
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: getComputedStyle(document.documentElement)
                                        .getPropertyValue('--text'),
                                    callback: value => fmoney(value)
                                }
                            }
                        },
                        onClick: (evt, elements) => {
                            if (!elements.length) return;
                            const index = elements[0].index; // 0=ingresos, 1=gastos ...

                            if (index === 0) {
                                renderCategorias(catsIngresos);
                            } else if (index === 1) {
                                renderCategorias(catsGastos);
                            } else {
                                renderCategorias([]);
                            }
                        }
                    }
                });

                // Por defecto mostramos gastos, por ejemplo
                renderCategorias(catsGastos);

            } else {
                // Tabs GASTOS / INGRESOS: barra apilada por categoría
                const cats = tipoVista === 'gastos' ? catsGastos : catsIngresos;

                const labels = ['{{ $label }}']; // 1 barra del período
                const datasets = cats.map(cat => ({
                    label: `${cat.nombre} (${cat.pct}%)`,
                    data: [cat.total],
                    backgroundColor: cat.color,
                    stack: 'stack1'
                }));

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: getComputedStyle(document.documentElement)
                                        .getPropertyValue('--text')
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const cat = cats[context.datasetIndex];
                                        return `${cat.nombre}: ${fmoney(cat.total)} (${cat.pct}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    color: getComputedStyle(document.documentElement)
                                        .getPropertyValue('--text')
                                },
                                grid: { display: false }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    color: getComputedStyle(document.documentElement)
                                        .getPropertyValue('--text'),
                                    callback: value => fmoney(value)
                                }
                            }
                        }
                    }
                });

                // En estos tabs mostramos directamente las categorías ya filtradas
                renderCategorias(catsInicial);
            }
        });
    </script>
@endsection
