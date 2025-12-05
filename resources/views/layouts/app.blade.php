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

    {{-- SweetAlert2 + Tema Oscuro --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link id="swal2-dark-theme"
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.css"
          disabled>

    {{-- Flatpickr con localización español --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    {{-- CSS + JS propios (incluye theme-switcher.js) --}}
    @vite(['resources/sass/app.scss','resources/js/app.js'])

    {{-- SweetAlert para notificaciones flash --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Listo',
                text: @json(session('success')),
                confirmButtonColor: '#6C63FF',
                theme: isDark ? 'dark' : undefined,
            });
            @endif

            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Ups…',
                text: @json(session('error')),
                confirmButtonColor: '#6C63FF',
                theme: isDark ? 'dark' : undefined,
            });
            @endif
        });
    </script>

    {{-- Estilos del layout (estructura general) --}}
    <style>
        html{ color-scheme: light dark }
        body{ margin:0; background:var(--bg); color:var(--text); font-family:'Nunito',system-ui,sans-serif; }
        .layout{ display:flex; min-height:100dvh; }

        /* === SIDEBAR === */
        .sidebar{
            position:fixed; inset:0 auto 0 0; width:var(--w-sidebar);
            background:var(--card); border-right:1px solid var(--divider); box-shadow:var(--shadow);
            transform:translateX(-100%); transition:.28s ease;
            display:flex; flex-direction:column; z-index:1000; overflow-y:auto;
        }
        .sidebar[data-open="true"]{ transform:translateX(0); }

        .sb-header{
            position:sticky; top:0; z-index:2;
            display:flex; align-items:center; gap:.6rem;
            padding:var(--pad-2); border-bottom:1px solid var(--divider);
            background: color-mix(in oklab, var(--beige) 88%, var(--card));
        }
        .sb-avatar{
            width:40px; height:40px; border-radius:50%;
            display:grid; place-items:center; font-size:1rem;
            background: color-mix(in oklab, var(--morado) 18%, var(--card));
            color: var(--morado);
        }
        .sb-title{ font-weight:800; font-size:var(--fs-1); }
        .sb-sub{ font-size:.9rem; color:var(--muted); }
        .sb-nav{ padding:.75rem var(--pad-2); display:flex; flex-direction:column; gap:.25rem; }

        .sb-link{
            display:flex; align-items:center; gap:.6rem;
            padding:.55rem .6rem; border-radius:.7rem;
            color:var(--text); text-decoration:none; font-weight:600;
            transition:.18s ease;
        }
        .sb-link i{ width:1.1rem; text-align:center; }

        .sb-link:hover{ background: color-mix(in oklab, var(--morado) 20%, transparent); color: var(--morado); }
        .sb-link.active{
            background: color-mix(in oklab, var(--morado) 22%, transparent);
            color: var(--morado); outline:2px solid color-mix(in oklab, var(--morado) 32%, transparent);
        }

        .sb-footer{
            margin-top:auto; padding:var(--pad-2);
            border-top:1px solid var(--divider);
            background: color-mix(in oklab, var(--beige) 94%, var(--card));
            display:flex; flex-direction:column; gap:.6rem;
        }

        .theme-row{ display:flex; align-items:center; justify-content:space-between; }

        .sb-cta{
            display:flex; align-items:center; justify-content:center; gap:.5rem;
            padding:.55rem .85rem; border:none; border-radius:999px;
            background:var(--morado); color:#fff; font-weight:800;
        }

        /* Overlay */
        .sidebar-overlay{
            position:fixed; inset:0; background:rgba(0,0,0,.35);
            opacity:0; visibility:hidden; transition:.2s;
        }
        .sidebar-overlay[data-show="true"]{ opacity:1; visibility:visible; }

        /* Toggle móvil */
        .sidebar-toggle{
            position:fixed; left:.6rem; top:.6rem; z-index:1100;
            border:none; background:var(--morado); color:#fff;
            padding:.5rem .7rem; border-radius:.7rem;
            display:flex; align-items:center; gap:.45rem;
        }

        .content{ flex:1; width:100%; padding:var(--pad-2); }
        @media(min-width:992px){
            .sidebar{ transform:none; }
            .sidebar-toggle{ display:none; }
            .content{ margin-left:var(--w-sidebar); }
        }
    </style>
</head>

<body>
<div id="app" class="layout">

    {{-- Botón móvil --}}
    <button class="sidebar-toggle" type="button">
        <i class="fa-solid fa-bars"></i> <span class="d-none d-sm-inline">Menú</span>
    </button>

    {{-- SIDEBAR LIMPIO --}}
    <aside id="sidebar" class="sidebar" data-open="false">
        <div class="sb-header">
            <div class="sb-avatar"><i class="fa-solid fa-user"></i></div>
            <div>
                @auth
                    <div class="sb-title">{{ auth()->user()->nombre }}</div>
                    <div class="sb-sub">Bienvenido</div>
                @else
                    <a href="{{ route('login') }}" class="sb-title">Iniciar sesión</a>
                @endauth
            </div>
        </div>

        {{-- Navegación --}}
        <nav class="sb-nav">
            <a href="{{ url('/home') }}" class="sb-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Inicio
            </a>

            <a href="{{ route('grafico.index') }}" class="sb-link {{ request()->is('grafico*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Gráficos
            </a>

            <a href="{{ route('categorias.index') }}" class="sb-link {{ request()->is('categorias*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Categorías
            </a>

            <a href="{{ route('consejos.consejos') }}" class="sb-link {{ request()->is('consejos*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i> Consejos
            </a>

            {{-- Se eliminaron Contacto, Ads, Premium, Recordatorios, Ajustes, etc. --}}
        </nav>

        {{-- Footer --}}
        <div class="sb-footer">
            <div class="theme-row">
                <span><i class="fa-solid fa-moon"></i> Modo oscuro</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="themeToggle">
                </div>
            </div>

            @auth
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="sb-cta">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </button>
                </form>
            @else
                <a class="sb-cta" href="{{ route('login') }}">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar
                </a>
            @endauth

            <div class="sb-sub">&copy; {{ date('Y') }} Spendly</div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Contenido de la página --}}
    <main class="content">
        @yield('content')
    </main>

</div>
</body>
</html>
