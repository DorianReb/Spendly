@extends('layouts.app')
@section('title','Nueva meta — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#777;
            --radius:1.2rem;
        }
        .meta-wrap{
            max-width:720px;
            margin-inline:auto;
            padding:.75rem .25rem 1.75rem;
        }
        .meta-topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
            margin-bottom:.75rem;
        }
        .meta-title{
            font-weight:800;
            letter-spacing:.02em;
            font-size:1.05rem;
        }
        .meta-sub{
            font-size:.9rem;
            color:var(--muted);
        }
        .chip-meta{
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
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }
        .form-label{
            font-weight:700;
            font-size:.9rem;
        }
        .form-text{
            font-size:.8rem;
            color:var(--muted);
        }
        .hint-box{
            border-radius:.9rem;
            padding:.75rem .8rem;
            background: color-mix(in oklab, var(--amarillo) 12%, transparent);
            font-size:.85rem;
            color:#5b4a20;
            margin-bottom:.9rem;
        }
    </style>

    <div class="meta-wrap">
        {{-- Topbar --}}
        <div class="meta-topbar">
            <a href="{{ route('metas.index') }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </a>
            <div class="text-end">
                <div class="meta-title">Nueva meta de ahorro</div>
                <div class="meta-sub">
                    <span class="chip-meta">
                        <i class="fa-solid fa-bullseye"></i>
                        Meta en curso
                    </span>
                </div>
            </div>
        </div>

        <section class="panel">
            {{-- Errores de validación --}}
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

            {{-- Pequeña ayuda --}}
            <div class="hint-box">
                <i class="fa-solid fa-lightbulb me-1"></i>
                Define un objetivo claro (por ejemplo, “Fondo de emergencia”) y una fecha límite
                aproximada para que Spendly pueda mostrar tu avance.
            </div>

            <form method="POST" action="{{ route('metas.store') }}">
                @csrf

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre de la meta</label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}"
                           maxlength="140"
                           required
                           placeholder="Ej. Fondo de emergencia, Viaje a la playa…">
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Sé específico: “Fondo de emergencia 3 meses”, “Laptop nueva”, etc.
                        </div>
                        @enderror
                </div>

                {{-- Objetivo --}}
                <div class="mb-3">
                    <label class="form-label" for="objetivo_mask">Monto objetivo (MXN)</label>

                    {{-- Input visible con formato --}}
                    <input type="text"
                           id="objetivo_mask"
                           class="form-control @error('objetivo') is-invalid @enderror"
                           value="{{ old('objetivo') }}"
                           placeholder="Ej. 10,000.00">

                    {{-- Input real que enviará el número --}}
                    <input type="hidden" name="objetivo" id="objetivo_real">

                    @error('objetivo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Cantidad total que quieres reunir para esta meta.</div>
                        @enderror
                </div>


                {{-- Fecha límite (opcional) --}}
                <div class="mb-3">
                    <label class="form-label" for="fecha_limite">Fecha límite (opcional)</label>
                    <input type="text"
                           name="fecha_limite"
                           id="fecha_limite"
                           class="form-control js-date-limit @error('fecha_limite') is-invalid @enderror"
                           value="{{ old('fecha_limite') }}"
                           autocomplete="off"
                           placeholder="Selecciona una fecha…">
                    @error('fecha_limite')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Puedes dejarlo en blanco si no tienes una fecha exacta.
                        </div>
                        @enderror
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('metas.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Guardar meta
                    </button>
                </div>
            </form>
        </section>
    </div>

    {{-- Flatpickr para fecha límite --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const maskInput = document.getElementById('objetivo_mask');
            const realInput = document.getElementById('objetivo_real');

            // --- 1. Flatpickr para la fecha límite ---
            if (window.flatpickr) {
                flatpickr('.js-date-limit', {
                    dateFormat: 'Y-m-d',
                    minDate: 'today',
                    allowInput: true,
                    locale: 'es'
                });
            }

            // --- 2. Formateo de dinero más amigable (solo al perder el foco) ---

            /**
             * Función auxiliar para limpiar y normalizar el valor a un número.
             * @param {string} value - Valor del input con formato de máscara.
             * @returns {number | null}
             */
            function cleanAndParse(value) {
                if (!value) return null;
                // 1. Reemplazar comas por puntos (si usaron coma como decimal)
                // 2. Eliminar todo lo que no sea dígito o punto.
                let clean = value.replace(/,/g, '').replace(/[^\d.]/g, '');

                let num = parseFloat(clean);
                return isNaN(num) ? null : num;
            }

            /**
             * Aplica formato local de dinero ($10,000.00) al input visible.
             * @param {number} num - Número limpio.
             * @returns {string}
             */
            function formatNumber(num) {
                if (num === null) return '';
                // Usamos 'en-US' para el formato 10,000.00 (miles con coma, decimales con punto)
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // A. Inicialización: Aplicar formato al cargar si hay 'old' data
            const initialValue = cleanAndParse(maskInput.value);
            if (initialValue !== null) {
                maskInput.value = formatNumber(initialValue);
                realInput.value = initialValue; // Aseguramos que el input oculto tenga el valor limpio
            }

            // B. Evento de Pérdida de Foco (Blur): Limpia y Formatea
            maskInput.addEventListener('blur', () => {
                const num = cleanAndParse(maskInput.value);

                if (num !== null) {
                    maskInput.value = formatNumber(num);
                    realInput.value = num;
                } else {
                    maskInput.value = '';
                    realInput.value = '';
                }
            });

            // C. Evento de Foco: Mostrar solo el número limpio para editar
            maskInput.addEventListener('focus', () => {
                const num = cleanAndParse(maskInput.value);
                if (num !== null) {
                    // Muestra el número limpio (sin comas de miles)
                    maskInput.value = num.toFixed(2);
                }
            });


            // --- 3. SweetAlert2 para errores ---
            @if ($errors->any())
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Revisa tu meta',
                    text: 'Hay errores en el formulario. Corrige los campos marcados.',
                    confirmButtonColor: '#6C63FF',
                    theme: 'dark'
                });
            }
            @endif
        });
    </script>


@endsection
