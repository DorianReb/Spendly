<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Spendly — Bienestar y balance')</title>

    {{-- Fonts + Icons (ya tienes FA en app.scss, pero lo dejo por si esta vista carga sola) --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Vite (Bootstrap + tu SCSS/JS) --}}
    @vite(['resources/sass/app.scss','resources/js/app.js'])

    <style>
        :root{
            --morado:#6C63FF;
            --amarillo:#FFCE45; /* más cálido, menos saturado */
            --beige:#FAF3DD;
            --gris:#2E2E2E;

            --bg:var(--beige);
            --text:var(--gris);
            --card:#ffffff;
            --muted:#666666;
            --divider: color-mix(in oklab, var(--text) 14%, transparent);

            --radius:1rem;
            --shadow:0 10px 28px rgba(0,0,0,.08);
            --w-sidebar:220px;
            --pad-1:.75rem;
            --pad-2:1rem;
            --fs-0:clamp(.95rem,.9rem + .25vw,1.05rem);
            --fs-1:clamp(1.1rem,1rem + .5vw,1.35rem);
        }

        html[data-theme="dark"]{
            --bg:#141217;
            --card:#1a171f;
            --text:#e6e6eb;
            --muted:#9d9daa;
            --divider: color-mix(in oklab, var(--text) 25%, transparent);

            /* Tonos adaptados al modo oscuro */
            --morado:#7d72ff;  /* un poco más claro para resaltar sobre fondo oscuro */
            --amarillo:#E9C66E; /* dorado suave, no chilla */
            --beige:#242026;    /* beige neutro, mantiene coherencia */
        }

        html{ color-scheme: light dark }
        body{ margin:0; background:var(--bg); color:var(--text); font-family:'Nunito',system-ui,sans-serif; }
        .layout{ display:flex; min-height:100dvh; }

        /* ===== Sidebar compacto ===== */
        .sidebar{
            position:fixed; inset:0 auto 0 0; width:var(--w-sidebar);
            background:var(--card); border-right:1px solid var(--divider); box-shadow:var(--shadow);
            transform:translateX(-100%); transition:transform .28s ease;
            display:flex; flex-direction:column; z-index:1000;
            overflow-y:auto; scrollbar-width:thin; scrollbar-gutter:stable;
        }
        .sidebar[data-open="true"]{ transform:translateX(0); }

        .sb-header{
            position:sticky; top:0; z-index:2;
            display:flex; align-items:center; gap:.6rem;
            padding: var(--pad-2); border-bottom:1px solid var(--divider);
            background: color-mix(in oklab, var(--beige) 88%, var(--card));
        }
        .sb-avatar{
            width:40px; height:40px; border-radius:50%;
            display:grid; place-items:center; font-size:1rem;
            background: color-mix(in oklab, var(--morado) 18%, var(--card));
            color: var(--morado);
            flex:0 0 auto;
        }
        .sb-title{ font-weight:800; font-size:var(--fs-1); line-height:1.05; }
        .sb-sub{ font-size:.9rem; color:var(--muted); }
        .link-inline{ color:var(--morado); text-decoration:none; font-weight:700; }
        .link-inline:hover{ text-decoration:underline; }

        .sb-nav{ padding: .5rem var(--pad-2) var(--pad-2); display:flex; flex-direction:column; gap:.25rem; }
        .sb-link{
            display:flex; align-items:center; gap:.6rem;
            padding:.55rem .6rem; border-radius:.7rem;
            color:var(--text); text-decoration:none; font-weight:600; font-size:.97rem;
            transition: background .18s ease, color .18s ease;
        }
        .sb-link i{ width:1.1rem; text-align:center; opacity:.95; }

        /* 👇 Hover morado y active morado como pediste */
        .sb-link:hover,
        .sb-link:focus{
            background: color-mix(in oklab, var(--morado) 20%, transparent);
            color: var(--morado);
            outline: none;
        }
        .sb-link.active{
            background: color-mix(in oklab, var(--morado) 22%, transparent);
            color: var(--morado);
            outline: 2px solid color-mix(in oklab, var(--morado) 32%, transparent);
        }

        .sb-sep{ height:1px; background:var(--divider); margin:.6rem var(--pad-2); }

        .sb-footer{
            position:sticky; bottom:0; z-index:1;
            padding: var(--pad-2); border-top:1px solid var(--divider);
            background: color-mix(in oklab, var(--beige) 94%, var(--card));
            display:flex; flex-direction:column; gap:.6rem;
        }
        .theme-row{ display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
        .theme-row .label{ display:flex; align-items:center; gap:.5rem; }

        .sb-cta{
            display:inline-flex; align-items:center; gap:.5rem; justify-content:center;
            padding:.55rem .85rem; border:none; border-radius:999px;
            background: var(--morado); color:#fff; font-weight:800;
            box-shadow:0 8px 22px rgba(108,99,255,.18);
            text-decoration:none;
        }
        .sb-cta.alt{ background: var(--amarillo); color:#3a3220; box-shadow:none; }

        /* Overlay móvil */
        .sidebar-overlay{
            position:fixed; inset:0; background:rgba(0,0,0,.35); backdrop-filter:blur(2px);
            opacity:0; visibility:hidden; transition:.2s; z-index:990;
        }
        .sidebar-overlay[data-show="true"]{ opacity:1; visibility:visible; }

        /* Botón hamburguesa */
        .sidebar-toggle{
            position:fixed; left:.6rem; top:.6rem; z-index:1100;
            border:none; background:var(--morado); color:#fff;
            padding:.5rem .7rem; border-radius:.7rem; display:flex; align-items:center; gap:.45rem;
            box-shadow:0 8px 22px rgba(108,99,255,.25);
        }

        /* Contenido */
        .content{ flex:1; width:100%; padding: var(--pad-2); }
        @media (min-width: 992px){
            .sidebar{ transform:none; }
            .sidebar-toggle{ display:none; }
            .content{ margin-left: var(--w-sidebar); }

            /* ===== Toggle morado personalizado ===== */
            .form-check-input:checked {
                background-color: var(--morado) !important;
                border-color: var(--morado) !important;
                box-shadow: 0 0 0 0.25rem color-mix(in oklab, var(--morado) 35%, transparent) !important;
            }

            .form-check-input:focus {
                border-color: var(--morado) !important;
                box-shadow: 0 0 0 0.25rem color-mix(in oklab, var(--morado) 25%, transparent) !important;
            }

            .form-check-input {
                cursor: pointer;
            }

        }
    </style>
</head>
<body>
<div id="app" class="layout">

    {{-- Toggle móvil --}}
    <button class="sidebar-toggle" type="button" aria-controls="sidebar" aria-expanded="false">
        <i class="fa-solid fa-bars"></i><span class="d-none d-sm-inline"> Menú</span>
    </button>

    {{-- Sidebar compacto --}}
    <aside id="sidebar" class="sidebar" data-open="false" aria-label="Navegación principal">
        <div class="sb-header">
            <div class="sb-avatar"><i class="fa-solid fa-user"></i></div>
            <div>
                @auth
                    <div class="sb-title">{{ auth()->user()->nombre ?? 'Usuario' }}</div>
                    <div class="sb-sub">Bienvenido</div>
                @else
                    <a href="{{ route('login') }}" class="sb-title link-inline">Iniciar sesión</a>
                    <div class="sb-sub">¿Nuevo? <a href="{{ route('register') }}" class="link-inline">Crear cuenta</a></div>
                @endauth
            </div>
        </div>

        <nav class="sb-nav" role="navigation">
            <a href="{{ url('/home') }}" class="sb-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i><span>Inicio</span>
            </a>
            {{--<a href="{{ url('/cuentas') }}" class="sb-link {{ request()->is('cuentas*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i><span>Cuentas</span>
            </a>
            --}}
            <a href="{{ url('/graficos') }}" class="sb-link {{ request()->is('graficos*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>Gráficos</span>
            </a>
            <a href="{{ url('/categorias') }}" class="sb-link {{ request()->is('categorias*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i><span>Categorías</span>
            </a>
            {{--<a href="{{ url('/pagos-habituales') }}" class="sb-link {{ request()->is('pagos-habituales*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrows-rotate"></i><span>Pagos habituales</span>
            </a>
            */--}}
            <a href="{{ url('/recordatorios') }}" class="sb-link {{ request()->is('recordatorios*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i><span>Recordatorios</span>
            </a>
            <a href="{{ url('/ajustes') }}" class="sb-link {{ request()->is('ajustes*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i><span>Ajustes</span>
            </a>

            <div class="sb-sep"></div>

            @auth
                @if(!(auth()->user()->es_premium ?? false))
                    <a href="{{ url('/premium') }}" class="sb-link">
                        <i class="fa-solid fa-rectangle-ad"></i><span>Desactivar anuncios</span>
                    </a>
                @endif
            @else
                <a href="{{ url('/premium') }}" class="sb-link">
                    <i class="fa-solid fa-rectangle-ad"></i><span>Desactivar anuncios</span>
                </a>
            @endauth

            {{--/*<a href="{{ url('/compartir') }}" class="sb-link">
                <i class="fa-solid fa-share-nodes"></i><span>Compartir con amigos</span>
            </a>
            <a href="{{ url('/valorar') }}" class="sb-link">
                <i class="fa-regular fa-star"></i><span>Valorar la aplicación</span>*/
            </a>
            --}}
            <a href="{{ url('/soporte') }}" class="sb-link {{ request()->is('soporte*') ? 'active' : '' }}">
                <i class="fa-regular fa-envelope"></i><span>Contacto</span>
            </a>
        </nav>

        <div class="sb-footer">
            <div class="theme-row">
                <div class="label"><i class="fa-solid fa-moon"></i><span>Modo oscuro</span></div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="themeToggle">
                </div>
            </div>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="sb-cta">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </button>
                </form>
            @else
                <div class="d-grid gap-2">
                    <a class="sb-cta alt" href="{{ route('login') }}"><i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar</a>
                    @if (Route::has('register'))
                        <a class="sb-cta" href="{{ route('register') }}"><i class="fa-solid fa-user-plus"></i> Crear cuenta</a>
                    @endif
                </div>
            @endauth

            <div class="sb-sub">&copy; {{ date('Y') }} {{ config('app.name','Spendly') }}</div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" data-show="false"></div>

    <main class="content">
        @yield('content')
    </main>
</div>

<script>
    // --- Sidebar ---
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    function openSidebar(){ sidebar.dataset.open="true"; overlay.dataset.show="true"; toggleBtn?.setAttribute('aria-expanded','true'); }
    function closeSidebar(){ sidebar.dataset.open="false"; overlay.dataset.show="false"; toggleBtn?.setAttribute('aria-expanded','false'); }

    toggleBtn?.addEventListener('click', ()=> (sidebar.dataset.open==="true") ? closeSidebar() : openSidebar());
    overlay?.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a.sb-link').forEach(a=>{
        a.addEventListener('click', ()=> { if (window.innerWidth < 992) closeSidebar(); });
    });
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && window.innerWidth<992) closeSidebar(); });

    // --- Tema persistente ---
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    if(themeToggle) themeToggle.checked = (savedTheme === 'dark');
    themeToggle?.addEventListener('change', ()=>{
        const t = themeToggle.checked ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);
    });
</script>
</body>
</html>
