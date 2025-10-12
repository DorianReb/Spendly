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

    <section id="app" class="container py-3 reveal">
        <div class="app-preview">
            <!-- Encabezado balance -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="text-muted small">Balance (Mes)</div>
                    <div class="fw-bold" style="font-size: clamp(1.25rem, 1.05rem + .9vw, 1.6rem)">$12,540.00</div>
                </div>
                <span class="badge-pill">+2.4% vs mes pasado</span>
            </div>

            <!-- KPIs fluidos -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <div class="kpi">
                        <div class="text-muted small">Ingresos</div>
                        <div class="fw-semibold" style="font-size: clamp(1rem, .9rem + .4vw, 1.125rem)">$18,250.00</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="kpi">
                        <div class="text-muted small">Gastos</div>
                        <div class="fw-semibold" style="font-size: clamp(1rem, .9rem + .4vw, 1.125rem)">$5,710.00</div>
                    </div>
                </div>
            </div>

            <!-- Meta destacada -->
            <div class="kpi mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Meta activa</div>
                    <div class="fw-semibold">Fondo de emergencia · 65% completado</div>
                </div>
                <a href="{{ route('register') }}" class="btn btn-sm btn-outline-dark">Ver metas</a>
            </div>

            <!-- Bottom “nav” demo -->
            <nav class="d-flex justify-content-around pt-2 border-top">
                <a class="text-decoration-none" href="{{ route('login') }}">Inicio</a>
                <a class="text-decoration-none" href="{{ route('login') }}">Movimientos</a>
                <a class="text-decoration-none fw-bold" href="{{ route('login') }}">＋</a>
                <a class="text-decoration-none" href="{{ route('login') }}">Metas</a>
                <a class="text-decoration-none" href="{{ route('login') }}">Reportes</a>
            </nav>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-cta">Crear cuenta y empezar</a>
            <a href="{{ route('login') }}" class="btn btn-cta btn-outline-dark ms-2">Ya tengo cuenta</a>
        </div>
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
