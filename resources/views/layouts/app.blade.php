<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Spendly — Bienestar y balance')</title>

    {{-- Fonts + Icons --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- 1. CARGA DE LIBRERÍAS DE TERCEROS (SweetAlert2 y Flatpickr) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tema oscuro de SweetAlert2 (deshabilitado por defecto) -->
    <link
        id="swal2-dark-theme"
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.css"
        disabled
    >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    {{-- 2. VITE (CARGA tu CSS global y tu JS modular, incluyendo theme-switcher.js) --}}
    @vite(['resources/sass/app.scss','resources/js/app.js'])

    {{-- 3. SweetAlert para mensajes flash (Con soporte para Modo Oscuro) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Listo',
                text: @json(session('success')),
                confirmButtonColor: '#6C63FF'
            });
            @endif

            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Ups…',
                text: @json(session('error')),
                confirmButtonColor: '#6C63FF'
            });
            @endif
        });
    </script>

    {{-- 4. Estilos de Layout (SÓLO ESTRUCTURA, las variables de color están en _theme.scss) --}}
    <style>
        /* Variables de color y correcciones de modo oscuro movidas a _theme.scss */

        html{ color-scheme: light dark }
        body{ margin:0; background:var(--bg); color:var(--text); font-family:'Nunito',system-ui,sans-serif; }
        .layout{ display:flex; min-height:100dvh; }

        /* ===== Sidebar compacto - Estructura ===== */
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

        /* Estilos de Hover/Active (Depende de variables CSS globales) */
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

        /* Contenido y Media Query */
        .content{ flex:1; width:100%; padding: var(--pad-2); }
        @media (min-width: 992px){
            .sidebar{ transform:none; }
            .sidebar-toggle{ display:none; }
            .content{ margin-left: var(--w-sidebar); }

            /* Toggle morado personalizado (Depende de variables CSS globales) */
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
            <a href="{{ url('/grafico') }}" class="sb-link {{ request()->is('grafico*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>Gráficos</span>
            </a>
            <a href="{{ route('categorias.index') }}" class="sb-link {{ request()->is('categorias*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i><span>Categorías</span>
            </a>

            {{--<a href="{{ url('/pagos-habituales') }}" class="sb-link {{ request()->is('pagos-habituales*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrows-rotate"></i><span>Pagos habituales</span>
            </a>
            */--}}
            {{--}}<a href="{{ url('/recordatorios') }}" class="sb-link {{ request()->is('recordatorios*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i><span>Recordatorios</span>
            </a>
            <a href="{{ url('/ajustes') }}" class="sb-link {{ request()->is('ajustes*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i><span>Ajustes</span>
            </a>
            {{--}}
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
{{-- LÓGICA DE TEMA Y SIDEBAR MOVIDA A resources/js/theme-switcher.js --}}
</body>
</html>
