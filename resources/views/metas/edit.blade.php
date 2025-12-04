@extends('layouts.app')
@section('title','Editar meta — Spendly')

@section('content')
    <style>
        .meta-edit-wrap{
            max-width: 700px;
            margin-inline:auto;
            padding:.75rem .25rem 1.5rem;
        }
        .meta-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:1rem;
        }
        .meta-title{
            font-weight:800; font-size:1.15rem;
        }

        .card-soft{
            background: var(--card);
            border-radius: var(--radius, 1rem);
            padding:1rem 1.1rem 1.3rem;
            box-shadow: var(--shadow, 0 10px 25px rgba(0,0,0,.06));
            border:1px solid var(--divider);
        }
        html[data-theme="dark"] .card-soft{
            box-shadow:0 10px 25px rgba(0,0,0,.40);
        }

        .form-label{ font-weight:700; font-size:.9rem; }
        .form-text{ font-size:.8rem; color:var(--muted); }
    </style>

    <div class="meta-edit-wrap">
        {{-- Topbar --}}
        <div class="meta-topbar">
            <a href="{{ route('metas.index') }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-chevron-left me-1"></i> Volver
            </a>
            <div class="meta-title">
                Editar meta
            </div>
        </div>

        <section class="card-soft">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <strong>Revisa los campos:</strong>
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
                           value="{{ old('nombre', $meta->nombre) }}"
                           maxlength="100"
                           required
                           placeholder="Ej. Viaje a Mérida">
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Un nombre corto y claro para identificar tu objetivo.
                        </div>
                        @enderror
                </div>

                {{-- Objetivo (monto) --}}
                @php
                    $objetivoRaw  = old('objetivo', $meta->objetivo);
                    $objetivoMask = number_format($objetivoRaw ?? 0, 2, '.', ',');
                @endphp

                <div class="mb-3">
                    <label class="form-label" for="objetivo_mask">Monto objetivo</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text"
                               id="objetivo_mask"
                               class="form-control @error('objetivo') is-invalid @enderror"
                               value="{{ $objetivoMask }}"
                               inputmode="decimal"
                               autocomplete="off">
                    </div>
                    {{-- campo real que se envía al backend --}}
                    <input type="hidden"
                           name="objetivo"
                           id="objetivo"
                           value="{{ $objetivoRaw }}">

                    @error('objetivo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Cantidad total que deseas alcanzar. Se formateará como 10,000.00; 100,000.00, etc.
                        </div>
                        @enderror
                </div>

                {{-- Fecha límite --}}
                <div class="mb-3">
                    <label class="form-label" for="fecha_limite">Fecha límite (opcional)</label>
                    <input type="text"
                           name="fecha_limite"
                           id="fecha_limite"
                           class="form-control js-date-limit @error('fecha_limite') is-invalid @enderror"
                           value="{{ old('fecha_limite', $meta->fecha_limite ? \Carbon\Carbon::parse($meta->fecha_limite)->toDateString() : '') }}"
                           autocomplete="off">
                    @error('fecha_limite')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Día en que te gustaría haber alcanzado esta meta (puede quedar vacío).
                        </div>
                        @enderror
                </div>

                {{-- Estado --}}
                <div class="mb-3">
                    <label class="form-label" for="estado">Estado</label>
                    <select name="estado"
                            id="estado"
                            class="form-select @error('estado') is-invalid @enderror"
                            required>
                        @foreach (['en_curso' => 'En curso', 'pausada' => 'Pausada', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('estado', $meta->estado) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">
                            Puedes marcarla como completada cuando alcances el objetivo,
                            pausarla o cancelarla si dejas de perseguirla.
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
            // --- Formateo de monto objetivo ---
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
                return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (inputMask && inputReal){
                // Inicial: asegurar coherencia
                inputMask.value = formatearMonto(inputReal.value || inputMask.value);

                inputMask.addEventListener('input', () => {
                    const limpio = limpiarNumero(inputMask.value);
                    inputReal.value = limpio;

                    inputMask.value = formatearMonto(inputMask.value);
                });
            }

            // --- Flatpickr para fecha límite (sin restricción adicional, valida el backend) ---
            if (window.flatpickr) {
                flatpickr('.js-date-limit', {
                    dateFormat: 'Y-m-d',
                    locale: 'es',
                    allowInput: true
                });
            }
        });
    </script>
@endsection
