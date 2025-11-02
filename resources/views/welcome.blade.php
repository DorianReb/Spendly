@extends('layouts.app')
@section('title','Spendly — Bienestar y balance')

@section('content')
    <style>
        /* Usa las variables ya definidas en el layout */
        .hero{
            min-height: 60vh;
            display: grid;
            place-items: center;
            text-align: center;
            padding: var(--pad-2);
        }
        .hero h1{
            font-size: clamp(1.6rem, 1.2rem + 2vw, 2.4rem);
            font-weight: 800;
            line-height: 1.1;
            margin: 0 0 .5em;
        }
        .hero p{
            font-size: clamp(1rem, .9rem + .4vw, 1.125rem);
            max-width: 60ch;
            margin-inline: auto;
            color: var(--muted);
        }

        .btn-primary{ background: var(--morado); border: none; font-weight: 700; }
        .btn-cta{ padding: .9em 1.6em; border-radius: 9999px; }
        .btn-amarillo{ background: var(--amarillo); color: #3a3220; border: none; font-weight: 700; }

        .app-preview{
            background: var(--card);
            border-radius: var(--radius, 1.25rem);
            padding: var(--pad-2);
            box-shadow: 0 10px 30px rgba(0,0,0,.04);
        }
        .badge-pill{
            display:inline-flex; align-items:center; gap:.4em;
            padding:.45em .9em; border-radius: 9999px;
            background: var(--amarillo); color:#3a3220; font-weight:700;
        }
        .kpi{
            border: 1px solid color-mix(in oklab, var(--text) 10%, transparent);
            border-radius: calc(var(--radius, 1.25rem) - .25rem);
            padding: var(--pad-1, 1rem);
        }

        @keyframes fade-up{ from{opacity:0; transform:translateY(.5rem)} to{opacity:1; transform:none} }
        .reveal{ opacity:0; transform: translateY(.5rem) }
        .reveal.is-in{ animation: fade-up .5s both }
        @media (prefers-reduced-motion: reduce){
            .reveal,.reveal.is-in{ animation:none; opacity:1; transform:none }
            html{ scroll-behavior: auto; }
        }

        /* ===== Botones morados personalizados ===== */
        .btn-primary,
        .btn-cta.btn-primary {
            background: var(--morado);
            border: none;
            font-weight: 700;
            color: #fff;
            transition: background .25s ease, transform .15s ease;
        }
        .btn-primary:hover,
        .btn-cta.btn-primary:hover {
            background: color-mix(in oklab, var(--morado) 85%, black 20%); /* morado más profundo */
            transform: translateY(-1px);
        }
        .btn-primary:active,
        .btn-cta.btn-primary:active {
            background: color-mix(in oklab, var(--morado) 75%, black 30%);
            transform: translateY(0);
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 0.25rem color-mix(in oklab, var(--morado) 40%, white 60%);
            outline: none;
        }


    </style>

    <section class="hero">
        <div class="reveal is-in">
            <h1>Equilibra tus finanzas, sin rodeos</h1>
            <p>Registra ingresos y gastos, define metas y visualiza tu progreso. Rápido, claro y en tu idioma.</p>
            <div class="d-flex gap-2 justify-content-center mt-2">
                <a href="{{ route('login') }}" class="btn btn-primary btn-cta">Entrar ahora</a>
                <a href="{{ route('register') }}" class="btn btn-cta btn-amarillo">Crear cuenta</a>
            </div>
        </div>
    </section>

    <section id="app" class="py-3">
        <style>
            /* usa las mismas variables del layout */
            :root{ --radius:1.2rem; }
            .home-wrap{ max-width:1100px; margin-inline:auto; padding-inline: var(--pad-2); }

            .topbar-mini{ display:flex; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:.75rem; }
            .pill{ background: color-mix(in oklab, var(--morado) 18%, transparent); color: var(--morado); padding:.35em .7em; border-radius:999px; font-weight:700; white-space:nowrap; }
            .total .amount{ font-size: clamp(1.35rem, 1rem + 4vw, 2.3rem); font-weight:800; line-height:1; }

            .tabs{ display:flex; gap:1rem; align-items:center; margin-bottom:.5rem; border-bottom:1px solid color-mix(in oklab, var(--text) 12%, transparent); overflow:auto; padding-bottom:.25rem; scroll-snap-type:x mandatory; }
            .tab{ position:relative; padding:.75rem .25rem; text-decoration:none; color: color-mix(in oklab, var(--text) 60%, transparent); font-weight:800; letter-spacing:.02em; white-space:nowrap; scroll-snap-align:center; }
            .tab.active{ color:var(--text); }
            .tab.active::after{ content:""; position:absolute; left:0; right:0; bottom:-1px; height:3px; background:var(--morado); border-radius:2px; }

            .subtabs{ display:flex; gap:1rem; overflow:auto; padding:.25rem 0 .5rem; scroll-snap-type:x proximity; margin-bottom:.5rem; }
            .subtabs a{ color:inherit; text-decoration:none; opacity:.75; font-weight:700; padding:.3rem .2rem; border-bottom:2px solid transparent; white-space:nowrap; scroll-snap-align:start; }
            .subtabs a.active{ color:var(--morado); opacity:1; border-color:var(--morado); }

            .panel{ background:var(--card); border-radius:1rem; padding: clamp(.8rem, .6rem + 1.5vw, 1.25rem); box-shadow:0 10px 28px rgba(0,0,0,.06); }
            .panel-header{ display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-bottom:.75rem; flex-wrap:wrap; }
            .panel-body{ display:grid; gap: clamp(.8rem, .6rem + 1.2vw, 1.2rem); grid-template-columns:1fr; }
            @media (min-width:768px){ .panel-body{ grid-template-columns:minmax(260px,380px) 1fr; align-items:center; } }

            .donut-wrap{ display:grid; place-items:center; padding:.25rem; }
            .donut{ width:min(78vw, 360px); aspect-ratio:1/1; position:relative; border-radius:50%;
                background: radial-gradient(circle at center,#333 0 38%, transparent 38%), conic-gradient(#ff4b4b var(--percent,65%), #242424 0%);
                box-shadow: inset 0 0 0 10px #3a3a3a, 0 8px 22px rgba(0,0,0,.25);
            }
            html[data-theme="dark"] .donut{
                background: radial-gradient(circle at center,#2b2b2b 0 38%, transparent 38%), conic-gradient(#ff4b4b var(--percent,65%), #1a1a1a 0%);
                box-shadow: inset 0 0 0 10px #454545, 0 8px 22px rgba(0,0,0,.35);
            }
            .donut-label{ position:absolute; inset:0; display:grid; place-items:center; color:#fff; font-weight:800; text-shadow:0 2px 12px rgba(0,0,0,.35); font-size: clamp(1.1rem, .9rem + 2.2vw, 1.7rem); pointer-events:none; }

            .list{ display:flex; flex-direction:column; gap:.6rem; }
            .item{ background: color-mix(in oklab, var(--text) 6%, transparent); border-radius:.9rem; padding: clamp(.65rem, .55rem + .5vw, .9rem); display:grid; grid-template-columns:auto 1fr auto; align-items:center; gap:.75rem; }
            html[data-theme="dark"] .item{ background: color-mix(in oklab, var(--text) 12%, transparent); }
            .item .icon{ width:36px; height:36px; border-radius:50%; display:grid; place-items:center; color:#fff; background:#ff6b6b; }
            .item .name{ font-weight:700; }
            .item .grow{ display:flex; align-items:center; gap: clamp(.5rem, .4rem + .4vw, 1rem); color:var(--muted); }
            .item-amount{ color:var(--text); font-weight:800; }

            .btn-primary{ background: var(--morado); border:none; font-weight:700; }
            .btn-primary:hover{ background: #5a4dfd; }
            .btn-cta{ padding:.9em 1.6em; border-radius:9999px; }
            .btn-amarillo{ background:var(--amarillo); color:#3a3220; border:none; font-weight:700; }
        </style>

        <div class="home-wrap">
            <!-- Top summary -->
            <div class="topbar-mini">
                <div class="pill"><i class="fa-solid fa-sack-dollar me-1"></i> Total</div>
                <div class="total"><div class="amount">$12,540.00</div></div>
                <div class="d-none d-md-block"><span class="badge bg-warning-subtle text-dark fw-bold">+2.4% vs mes pasado</span></div>
            </div>

            <!-- Tabs -->
            <div class="tabs" id="tabs">
                <a href="#" class="tab active" data-tab="gastos">GASTOS</a>
                <a href="#" class="tab" data-tab="ingresos">INGRESOS</a>
                <a href="#" class="tab" data-tab="metas">METAS</a>
            </div>

            <!-- Sub-filtros -->
            <div class="subtabs" id="subtabs">
                <a href="#" class="active" data-range="dia">Día</a>
                <a href="#" data-range="semana">Semana</a>
                <a href="#" data-range="mes">Mes</a>
                <a href="#" data-range="anio">Año</a>
                <a href="#" data-range="periodo">Período</a>
            </div>

            <!-- Panel principal -->
            <section class="panel">
                <div class="panel-header">
                    <div class="range"><small class="text-muted"><span id="rangeLabel">6 oct – 12 oct</span></small></div>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark">Entrar para agregar</a>
                </div>

                <div class="panel-body">
                    <!-- Donut -->
                    <div class="donut-wrap">
                        <div class="donut" id="donut" style="--percent:80%;">
                            <div class="donut-label" id="donutLabel">$2,450</div>
                        </div>
                    </div>

                    <!-- Lista -->
                    <div class="list" id="lista">
                        <div class="item">
                            <div class="icon" style="background:#ff6b6b"><i class="fa-solid fa-utensils"></i></div>
                            <div class="name">Comida</div>
                            <div class="grow"><span>45 %</span><span class="item-amount">$1,100</span><i class="fa-solid fa-chevron-right"></i></div>
                        </div>
                        <div class="item">
                            <div class="icon" style="background:#ffa94d"><i class="fa-solid fa-bus"></i></div>
                            <div class="name">Transporte</div>
                            <div class="grow"><span>25 %</span><span class="item-amount">$600</span><i class="fa-solid fa-chevron-right"></i></div>
                        </div>
                        <div class="item">
                            <div class="icon" style="background:#748ffc"><i class="fa-solid fa-house"></i></div>
                            <div class="name">Renta</div>
                            <div class="grow"><span>30 %</span><span class="item-amount">$750</span><i class="fa-solid fa-chevron-right"></i></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA inferior -->
            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="btn btn-primary btn-cta">Crear cuenta y empezar</a>
                <a href="{{ route('login') }}" class="btn btn-cta btn-outline-dark ms-2">Ya tengo cuenta</a>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const donut = document.getElementById("donut");
                const donutLabel = document.getElementById("donutLabel");

                // Datos de ejemplo
                const data = [
                    { name: "Comida", color: "#ff6b6b", value: 1100 },
                    { name: "Transporte", color: "#ffa94d", value: 600 },
                    { name: "Renta", color: "#748ffc", value: 750 }
                ];

                // Calcular total y porcentajes
                const total = data.reduce((acc, d) => acc + d.value, 0);
                let gradientParts = [];
                let acc = 0;

                data.forEach((d) => {
                    const percent = (d.value / total) * 100;
                    const start = acc;
                    const end = acc + percent;
                    gradientParts.push(`${d.color} ${start}% ${end}%`);
                    acc = end;
                });

                // Crear el conic-gradient dinámico
                const gradient = `conic-gradient(${gradientParts.join(", ")})`;

                // Aplicar al fondo del donut
                donut.style.background = `
            radial-gradient(circle at center, var(--card) 0 38%, transparent 38%),
            ${gradient}
        `;

                // Mostrar total al centro
                donutLabel.textContent = `$${total.toLocaleString()}`;
            });
        </script>

    </section>


    <footer class="container text-center py-3">
        <small>© {{ date('Y') }} Spendly — Bienestar y Balance</small>
    </footer>

    <script>
        // Aparición por viewport
        const io = new IntersectionObserver(es=>{
            es.forEach(e=> e.isIntersecting && e.target.classList.add('is-in'))
        },{threshold:.12});
        document.querySelectorAll('.reveal').forEach(el=> io.observe(el));
    </script>
@endsection
