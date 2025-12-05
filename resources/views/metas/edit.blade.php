@extends('layouts.app')
@section('title','Editar meta — Spendly')

@section('content')
    <style>
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
    </style>

    @php
        // Valores antiguos o actuales de la meta
        $nombreOld      = old('nombre', $meta->nombre);
        $objetivoOld    = old('objetivo', $meta->objetivo);
        $estadoOld      = old('estado', $meta->estado);
        $aporteOld      = old('aporte_mensual_sugerido', $meta->aporte_mensual_sugerido);

        $fechaLimiteOld = old(
            'fecha_limite',
            $meta->fecha_limite
                ? \Carbon\Carbon::parse($meta->fecha_limite)->format('d/m/Y')
                : ''
        );

        // Máscara formateada para el input visible
        $objetivoMask = ($objetivoOld !== null && $objetivoOld !== '')
            ? number_format($objetivoOld, 2, '.', ',')
            : '';
    @endphp

    <div class="meta-wrap">
        {{-- Topbar --}}
        <div class="meta-topbar">
            <a href="{{ route('metas.index') }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </a>
            <div class="text-end">
                <div class="meta-title">Editar meta de ahorro</div>
                <div class="meta-sub">
                    <span class="chip-meta">
                        <i class="fa-solid fa-bullseye"></i>
                        Editando: {{ $meta->nombre }}
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

            <form method="POST" action="{{ route('metas.update', $meta) }}">
                @csrf
                @method('PUT')

                {{-- Nombre --}}
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre de la meta</label>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ $nombreOld }}"
                           maxlength="140"
                           required>
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Puedes ajustar el nombre si lo deseas.
                        </div>
                        @enderror
                </div>

                {{-- Objetivo (monto) --}}
                <div class="mb-3">
                    <label class="form-label" for="objetivo_mask">Monto objetivo (MXN)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text"
                               id="objetivo_mask"
                               class="form-control @error('objetivo') is-invalid @enderror"
                               value="{{ $objetivoMask }}"
                               inputmode="decimal"
                               autocomplete="off">
                    </div>

                    {{-- Campo real que se envía al backend --}}
                    <input type="hidden"
                           name="objetivo"
                           id="objetivo"
                           value="{{ $objetivoOld }}">

                    @error('objetivo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Modifica el objetivo si cambió el monto que deseas alcanzar.
                        </div>
                        @enderror
                </div>

                {{-- Fecha límite (opcional) --}}
                <div class="mb-3">
                    <label class="form-label" for="fecha_limite">Fecha límite (opcional)</label>
                    <input type="text"
                           name="fecha_limite"
                           id="fecha_limite"
                           class="form-control js-date-limit @error('fecha_limite') is-invalid @enderror"
                           value="{{ $fechaLimiteOld }}"
                           autocomplete="off">
                    @error('fecha_limite')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Fecha estimada para cumplir la meta. Puedes dejarlo en blanco.
                        </div>
                        @enderror
                </div>

                {{-- Aporte mensual sugerido (opcional) --}}
                <div class="mb-3">
                    <label class="form-label" for="aporte_mensual_sugerido">Aporte mensual sugerido (opcional)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="aporte_mensual_sugerido"
                               id="aporte_mensual_sugerido"
                               class="form-control @error('aporte_mensual_sugerido') is-invalid @enderror"
                               value="{{ $aporteOld }}">
                    </div>
                    @error('aporte_mensual_sugerido')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Solo como referencia para tu planificación mensual.
                        </div>
                        @enderror
                </div>

                {{-- Estado --}}
                <div class="mb-3">
                    <label class="form-label" for="estado">Estado de la meta</label>
                    <select name="estado"
                            id="estado"
                            class="form-select @error('estado') is-invalid @enderror"
                            required>
                        @foreach(['en_curso' => 'En curso',
                                  'pausada' => 'Pausada',
                                  'completada' => 'Completada',
                                  'cancelada' => 'Cancelada'] as $value => $text)
                            <option value="{{ $value }}"
                                {{ $estadoOld === $value ? 'selected' : '' }}>
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Actualiza el estado según el avance de tu meta.
                        </div>
                        @enderror
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('metas.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- 1. Máscara de monto objetivo (igual lógica que en create) ---
            const inputMask = document.getElementById('objetivo_mask');
            const inputReal = document.getElementById('objetivo');

            function limpiarNumero(str){
                if (!str) return '';
                return str.replace(/,/g,'').trim();
            }

            function formatearMonto(valor){
                const limpio = limpiarNumero(valor);
                if (limpio === '' || isNaN(limpio)) return '';
                const num = parseFloat(limpio);
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            if (inputMask && inputReal){
                // Inicial: asegurar coherencia entre ambos campos
                inputMask.value = formatearMonto(inputReal.value || inputMask.value);

                inputMask.addEventListener('input', () => {
                    const limpio = limpiarNumero(inputMask.value);
                    inputReal.value = limpio;
                    inputMask.value = formatearMonto(inputMask.value);
                });
            }

            // --- 2. Flatpickr para la fecha límite ---
            if (window.flatpickr) {
                const fechaField = document.getElementById('fecha_limite');
                flatpickr(fechaField, {
                    dateFormat: 'd/m/Y',
                    locale: 'es',
                    allowInput: true,
                    defaultDate: fechaField.value || null
                });
            }

            // --- 3. SweetAlert2 para errores (opcional, igual que en create) ---
            @if ($errors->any())
            if (window.Swal) {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

                const opts = {
                    icon: 'error',
                    title: 'Revisa tu meta',
                    text: 'Hay errores en el formulario. Corrige los campos marcados.',
                    confirmButtonColor: '#6C63FF'
                };

                if (isDark) {
                    opts.theme = 'dark';
                }

                Swal.fire(opts);
            }
            @endif
        });
    </script>
@endsection
