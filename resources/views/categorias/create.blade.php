@extends('layouts.app')
@section('title','Nueva categoría — Spendly')

@section('content')
    <style>
        .cats-wrap{
            max-width:680px;
            margin-inline:auto;
            padding:.5rem .25rem 1.5rem;
        }

        .cats-topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
            margin-bottom:.75rem;
        }
        .cats-title{
            font-weight:800;
            letter-spacing:.02em;
            font-size:1.05rem;
        }
        .cats-sub{
            font-size:.9rem;
            color:var(--muted);
        }

        .pill-tipo{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            padding:.25rem .7rem;
            border-radius:999px;
            font-size:.8rem;
            font-weight:700;
            letter-spacing:.06em;
            text-transform:uppercase;
            background: color-mix(in oklab, var(--morado) 14%, transparent);
            color: var(--morado);
        }

        .panel{
            background: var(--card);
            border-radius: 1rem;
            padding: 1rem 1.1rem 1.3rem;
            border: 1px solid color-mix(in oklab, var(--text) 10%, transparent);
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }
        html[data-theme="dark"] .panel{
            border-color: var(--divider);
            box-shadow: 0 10px 28px rgba(0,0,0,.4);
        }

        .form-label{
            font-weight:700;
            font-size:.9rem;
        }
        .form-text{
            font-size:.8rem;
            color:var(--muted);
        }

        .color-row{
            display:flex;
            align-items:center;
            gap:.75rem;
        }
        .color-preview{
            width:40px;
            height:40px;
            border-radius:999px;
            box-shadow:0 8px 18px rgba(0,0,0,.12);
            border:2px solid rgba(255,255,255,.75);
        }
        html[data-theme="dark"] .color-preview{
            border-color: rgba(255,255,255,.25);
        }

        .btn-ghost{
            border-radius:999px;
            border:1px solid color-mix(in oklab, var(--text) 20%, transparent);
            background:transparent;
            font-weight:600;
            padding:.45rem 1rem;
            color:var(--text);
        }
        html[data-theme="dark"] .btn-ghost{
            border-color: var(--divider);
        }
        .btn-ghost:hover{
            background:color-mix(in oklab, var(--morado) 10%, transparent);
            color:var(--morado);
        }

        /* Iconos */
        .icon-option.active{
            outline:2px solid var(--morado);
        }
    </style>

    <div class="cats-wrap">

        {{-- Topbar --}}
        <div class="cats-topbar">
            <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </a>

            <div class="text-end">
                <div class="cats-title">Nueva categoría</div>
                <div class="cats-sub">
                    Tipo:
                    <span class="pill-tipo">
                        @if($tipo === 'gasto')
                            <i class="fa-solid fa-arrow-trend-down"></i> Gasto
                        @else
                            <i class="fa-solid fa-arrow-trend-up"></i> Ingreso
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Panel --}}
        <section class="panel">

            {{-- Errores --}}
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <strong>Ups…</strong> revisa los campos marcados.
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('categorias.store') }}">
                @csrf

                <input type="hidden" name="tipo" value="{{ $tipo }}">

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label">Nombre de la categoría</label>
                    <input type="text"
                           name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           maxlength="120"
                           required>
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Debe ser único dentro de tus {{ $tipo }}s.</div>
                        @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label class="form-label">Descripción (opcional)</label>
                    <textarea name="descripcion"
                              rows="2"
                              maxlength="255"
                              class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Solo para que tú recuerdes mejor el uso.</div>
                        @enderror
                </div>

                {{-- Icono --}}
                @php $iconDefault = old('icon', 'fa-wallet'); @endphp

                <div class="mb-3">
                    <label class="form-label d-block">Símbolo</label>

                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div id="symbolPreview"
                             class="color-preview d-flex align-items-center justify-content-center"
                             style="background: {{ old('color_hex', '#6C63FF') }}; width:48px; height:48px;">
                            <i id="iconPreview" class="fa-solid {{ $iconDefault }}" style="font-size:1.4rem;"></i>
                        </div>
                        <div class="form-text">Este ícono aparecerá en la lista de categorías.</div>
                    </div>

                    {{-- iconos rápidos --}}
                    <div class="d-flex flex-wrap gap-2 mb-2" id="quickIcons">
                        @foreach(['fa-wallet','fa-cart-shopping','fa-heart-pulse','fa-bus','fa-house','fa-mug-saucer','fa-gamepad','fa-graduation-cap'] as $icon)
                            <button type="button"
                                    class="btn btn-light btn-sm icon-option {{ $iconDefault === $icon ? 'active' : '' }}"
                                    data-icon="{{ $icon }}"
                                    style="border-radius:999px; width:40px; height:40px; display:grid; place-items:center;">
                                <i class="fa-solid {{ $icon }}"></i>
                            </button>
                        @endforeach

                        <button type="button"
                                class="btn btn-warning btn-sm"
                                style="border-radius:999px; width:40px; height:40px; font-weight:700;"
                                data-bs-toggle="modal"
                                data-bs-target="#iconCatalogModal">...</button>
                    </div>

                    <input type="hidden" name="icon" id="iconInput" value="{{ $iconDefault }}">
                </div>

                {{-- Color --}}
                @php $defaultColor = old('color_hex', '#6C63FF'); @endphp

                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <div class="color-row">
                        <div class="color-preview" id="colorPreview" style="background: {{ $defaultColor }}"></div>

                        <input type="color"
                               name="color_hex"
                               id="color_hex"
                               class="form-control form-control-color @error('color_hex') is-invalid @enderror"
                               value="{{ $defaultColor }}">
                    </div>
                    @error('color_hex')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text">Será el color del círculo que representa la categoría.</div>
                        @enderror
                </div>

                {{-- Activa --}}
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="activa" name="activa" checked disabled>
                    <label class="form-check-label">Categoría activa</label>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('categorias.index') }}" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Guardar categoría
                    </button>
                </div>
            </form>
        </section>
    </div>

    {{-- === Modal catálogo de iconos === --}}
    <div class="modal fade" id="iconCatalogModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Catálogo de iconos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @php
                        $secciones = [
                            'Finanzas' => ['fa-piggy-bank','fa-sack-dollar','fa-coins','fa-chart-line','fa-receipt','fa-hand-holding-dollar','fa-calculator','fa-percent'],
                            'Transporte' => ['fa-bus','fa-car','fa-taxi','fa-train','fa-plane','fa-ship','fa-motorcycle','fa-bicycle'],
                            'Compras' => ['fa-cart-shopping','fa-bag-shopping','fa-tag','fa-gift','fa-shirt','fa-shoe-prints','fa-mobile-screen','fa-camera'],
                            'Hogar y servicios' => ['fa-house','fa-lightbulb','fa-droplet','fa-fire-flame-simple','fa-soap','fa-screwdriver-wrench','fa-paint-roller','fa-sink'],
                            'Salud y bienestar' => ['fa-heart-pulse','fa-briefcase-medical','fa-pills','fa-spa','fa-person-running','fa-dumbbell','fa-tooth','fa-stethoscope'],
                            'Entretenimiento' => ['fa-gamepad','fa-ticket','fa-music','fa-film','fa-champagne-glasses','fa-tv','fa-clapperboard','fa-dice'],
                            'Educación' => ['fa-graduation-cap','fa-book','fa-book-open','fa-laptop-code','fa-pencil','fa-school','fa-chalkboard','fa-globe'],
                        ];
                    @endphp

                    @foreach($secciones as $titulo => $icons)
                        <h6 class="mt-2 mb-2">{{ $titulo }}</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($icons as $icon)
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm icon-option"
                                        data-icon="{{ $icon }}"
                                        style="border-radius:999px; width:40px; height:40px; display:grid; place-items:center;">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    {{-- === JS === --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- COLOR ---
            const colorInput    = document.getElementById('color_hex');
            const colorPreview  = document.getElementById('colorPreview');
            const symbolPreview = document.getElementById('symbolPreview');

            const updateColor = () => {
                const c = colorInput.value || '#6C63FF';
                colorPreview.style.background  = c;
                symbolPreview.style.background = c;
            };
            colorInput.addEventListener('input', updateColor);
            updateColor(); // inicial

            // --- ICONOS ---
            const iconInput   = document.getElementById('iconInput');
            const iconPreview = document.getElementById('iconPreview');

            document.querySelectorAll('.icon-option').forEach(btn => {
                btn.addEventListener('click', () => {
                    const icon = btn.dataset.icon;
                    if (!icon) return;

                    iconInput.value = icon;
                    iconPreview.className = 'fa-solid ' + icon;

                    // Marcar activo
                    document.querySelectorAll('.icon-option').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Cerrar modal si se seleccionó dentro de él
                    const modalEl = btn.closest('.modal');
                    if (modalEl && window.bootstrap) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal && modal.hide();
                    }
                });
            });

            // --- SweetAlert2 para errores de validación ---
            @if ($errors->any() && !session('success'))
            if (window.Swal) {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

                const options = {
                    icon: 'error',
                    title: 'Revisa la categoría',
                    text: 'Hay errores en el formulario. Corrige los campos marcados.',
                    confirmButtonColor: '#6C63FF'
                };

                // Sólo aplicamos el theme dark cuando el layout está en modo oscuro
                if (isDark) {
                    options.theme = 'dark';
                }

                Swal.fire(options);
            }
            @endif
        });
    </script>
@endsection
