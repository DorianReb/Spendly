@extends('layouts.app')
@section('title','Nueva categoría — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#8b8b8b;
            --radius:1.2rem;
        }

        .cats-wrap{ max-width:680px; margin-inline:auto; padding:.5rem .25rem 1.5rem; }

        .cats-topbar{
            display:flex; align-items:center; justify-content:space-between; gap:.75rem;
            margin-bottom:.75rem;
        }
        .cats-title{ font-weight:800; letter-spacing:.02em; font-size:1.05rem; }
        .cats-sub{ font-size:.9rem; color:var(--muted); }

        .pill-tipo{
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.25rem .7rem; border-radius:999px;
            font-size:.8rem; font-weight:700; letter-spacing:.06em;
            text-transform:uppercase;
            background: color-mix(in oklab, var(--morado) 14%, transparent);
            color: var(--morado);
        }

        .panel{
            background: var(--card); border-radius: 1rem; padding: 1rem 1.1rem 1.3rem;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }

        .form-label{ font-weight:700; font-size:.9rem; }
        .form-text{ font-size:.8rem; color:var(--muted); }

        .color-row{
            display:flex; align-items:center; gap:.75rem;
        }
        .color-preview{
            width:40px; height:40px; border-radius:999px;
            box-shadow:0 8px 18px rgba(0,0,0,.12);
            border:2px solid #fff;
        }

        .btn-ghost{
            border-radius:999px;
            border:1px solid color-mix(in oklab, var(--text) 20%, transparent);
            background:transparent;
            font-weight:600;
            padding:.45rem 1rem;
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

        {{-- Panel con formulario --}}
        <section class="panel">
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

            <form method="POST" action="{{ route('categorias.store') }}">
                @csrf

                {{-- Tipo (oculto, viene de la URL) --}}
                <input type="hidden" name="tipo" value="{{ $tipo }}">

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre de la categoría</label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           maxlength="120"
                           required
                           placeholder="Ej. Salud, Transporte, Salario">
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Debe ser único dentro de tus {{ $tipo }}s.</div>
                        @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label class="form-label" for="descripcion">Descripción (opcional)</label>
                    <textarea name="descripcion"
                              id="descripcion"
                              rows="2"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              maxlength="255"
                              placeholder="Ej. Gastos médicos, consultas, farmacia…">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Solo para que tú recuerdes mejor el uso de la categoría.</div>
                        @enderror
                </div>

                @php
                    $iconDefault = old('icon', 'fa-wallet');
                @endphp

                {{-- Icono --}}
                <div class="mb-3">
                    <label class="form-label d-block">Símbolo</label>

                    {{-- preview grande --}}
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div id="symbolPreview"
                             class="color-preview d-flex align-items-center justify-content-center"
                             style="background: {{ old('color_hex', '#6C63FF') }}; width:48px; height:48px;">
                            <i id="iconPreview" class="fa-solid {{ $iconDefault }}" style="font-size:1.4rem;"></i>
                        </div>

                        <div class="form-text">Este ícono aparecerá en el círculo de la categoría.</div>
                    </div>

                    {{-- íconos rápidos --}}
                    <div class="d-flex flex-wrap gap-2 mb-2" id="quickIcons">
                        @foreach([
                            'fa-wallet', 'fa-cart-shopping', 'fa-heart-pulse', 'fa-bus',
                            'fa-house', 'fa-mug-saucer', 'fa-gamepad', 'fa-graduation-cap',
                        ] as $icon)
                            <button type="button"
                                    class="btn btn-light btn-sm icon-option {{ $iconDefault === $icon ? 'active' : '' }}"
                                    data-icon="{{ $icon }}"
                                    style="border-radius:999px; width:40px; height:40px; display:grid; place-items:center;">
                                <i class="fa-solid {{ $icon }}"></i>
                            </button>
                        @endforeach

                        {{-- botón "..." para catálogo completo --}}
                        <button type="button"
                                class="btn btn-warning btn-sm"
                                style="border-radius:999px; width:40px; height:40px; font-weight:700;"
                                data-bs-toggle="modal"
                                data-bs-target="#iconCatalogModal">
                            ...
                        </button>
                    </div>

                    {{-- input oculto real --}}
                    <input type="hidden" name="icon" id="iconInput" value="{{ $iconDefault }}">

                    @error('icon')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Color --}}
                @php
                    $defaultColor = old('color_hex', '#6C63FF');
                @endphp
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <div class="color-row">
                        <div class="color-preview" id="colorPreview" style="background: {{ $defaultColor }};"></div>
                        <input type="color"
                               name="color_hex"
                               id="color_hex"
                               class="form-control form-control-color @error('color_hex') is-invalid @enderror"
                               value="{{ $defaultColor }}"
                               title="Elige un color">
                    </div>
                    @error('color_hex')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text">Este color se usará para el círculo de la categoría.</div>
                        @enderror
                </div>

                {{-- Activa --}}
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" checked disabled>
                    <label class="form-check-label" for="activa">Categoría activa</label>
                    <div class="form-text">Por ahora todas las nuevas categorías se crean activas.</div>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('categorias.index') }}" class="btn btn-ghost">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Guardar categoría
                    </button>
                </div>
            </form>
        </section>
    </div>

    {{-- === MODAL CATÁLOGO DE ICONOS === --}}
    <div class="modal fade" id="iconCatalogModal" tabindex="-1" aria-labelledby="iconCatalogLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="iconCatalogLabel">Catálogo de iconos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @php
                        $secciones = [
                            'Finanzas' => [
                                'fa-piggy-bank','fa-sack-dollar','fa-coins','fa-chart-line',
                                'fa-receipt','fa-hand-holding-dollar','fa-calculator','fa-percent',
                            ],
                            'Transporte' => [
                                'fa-bus','fa-car','fa-taxi','fa-train','fa-plane','fa-ship',
                                'fa-motorcycle','fa-bicycle',
                            ],
                            'Compras' => [
                                'fa-cart-shopping','fa-bag-shopping','fa-tag','fa-gift',
                                'fa-shirt','fa-shoe-prints','fa-mobile-screen','fa-camera',
                            ],
                            'Hogar y servicios' => [
                                'fa-house','fa-lightbulb','fa-droplet','fa-fire-flame-simple',
                                'fa-soap','fa-screwdriver-wrench','fa-paint-roller','fa-sink',
                            ],
                            'Salud y bienestar' => [
                                'fa-heart-pulse','fa-briefcase-medical','fa-pills','fa-spa',
                                'fa-person-running','fa-dumbbell','fa-tooth','fa-stethoscope',
                            ],
                            'Entretenimiento' => [
                                'fa-gamepad','fa-ticket','fa-music','fa-film',
                                'fa-champagne-glasses','fa-tv','fa-clapperboard','fa-dice',
                            ],
                            'Educación' => [
                                'fa-graduation-cap','fa-book','fa-book-open','fa-laptop-code',
                                'fa-pencil','fa-school','fa-chalkboard','fa-globe',
                            ],
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

    {{-- === JS: color + selección de iconos === --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Preview de color dinámico
            const inputColor     = document.getElementById('color_hex');
            const previewColor   = document.getElementById('colorPreview');   // cuadrito de "Color"
            const symbolPreview  = document.getElementById('symbolPreview');  // círculo del símbolo

            if (inputColor) {
                const updateColor = () => {
                    const c = inputColor.value || '#6C63FF';
                    if (previewColor)  previewColor.style.background  = c;
                    if (symbolPreview) symbolPreview.style.background = c;
                };

                // al cargar
                updateColor();

                // cuando el usuario cambia el color
                inputColor.addEventListener('input', updateColor);
            }


            const iconInput   = document.getElementById('iconInput');
            const iconPreview = document.getElementById('iconPreview');

            document.querySelectorAll('.icon-option').forEach(btn => {
                btn.addEventListener('click', () => {
                    const iconClass = btn.dataset.icon;
                    if (!iconClass) return;

                    iconInput.value = iconClass;
                    iconPreview.className = 'fa-solid ' + iconClass;

                    document.querySelectorAll('.icon-option').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // si viene de un modal, lo cerramos
                    const modalEl = btn.closest('.modal');
                    if (modalEl && window.bootstrap) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal && modal.hide();
                    }
                });
            });
        });
    </script>
@endsection
