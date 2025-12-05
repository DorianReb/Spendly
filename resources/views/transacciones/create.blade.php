@extends('layouts.app')
@section('title','Nueva transacción — Spendly')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#777;
            --radius:1.2rem;
        }
        .trx-wrap{ max-width:700px; margin-inline:auto; padding:.5rem .25rem 1.5rem; }
        .trx-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:.75rem;
        }
        .trx-title{ font-weight:800; letter-spacing:.02em; font-size:1.05rem; }
        .trx-sub{ font-size:.9rem; color:var(--muted); }
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
    </style>

    <div class="trx-wrap">
        {{-- Topbar --}}
        <div class="trx-topbar">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </a>
            <div class="text-end">
                <div class="trx-title">Nueva transacción</div>
                <div class="trx-sub">
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

            @php
                // Traemos las categorías del usuario directamente aquí
                $categorias = \App\Models\Categoria::where('usuario_id', auth()->id())
                    ->where('tipo', $tipo)
                    ->where('activa', 1)
                    ->orderBy('orden')
                    ->get();
            @endphp

            <form method="POST" action="{{ route('transacciones.store') }}">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">

                {{-- Importe --}}
                <div class="mb-3">
                    <label class="form-label" for="importe">Cantidad</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="importe"
                           id="importe"
                           class="form-control @error('importe') is-invalid @enderror"
                           value="{{ old('importe') }}"
                           required
                           placeholder="Ej. 500.00">
                    @error('importe')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Monto del {{ $tipo }} en MXN.</div>
                        @enderror
                </div>

                {{-- Categoría --}}
                <div class="mb-3">
                    <label class="form-label" for="categoria_id">Categoría</label>
                    <select name="categoria_id" id="categoria_id"
                            class="form-select @error('categoria_id') is-invalid @enderror"
                            required>
                        <option value="">Selecciona una categoría…</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">En qué categoría clasificar este {{ $tipo }}.</div>
                        @enderror
                </div>

                {{-- Fecha --}}
                <div class="mb-3">
                    <label class="form-label" for="fecha">Fecha</label>
                    <input type="text"
                           name="fecha"
                           id="fecha"
                           class="form-control js-date @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', now()->format('d/m/Y')) }}"
                           required
                           autocomplete="off">
                    @error('fecha')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Puedes registrar una fecha distinta al día de hoy.</div>
                        @enderror
                </div>

                {{-- Nota --}}
                <div class="mb-3">
                    <label class="form-label" for="nota">Comentario (opcional)</label>
                    <textarea name="nota"
                              id="nota"
                              rows="2"
                              class="form-control @error('nota') is-invalid @enderror"
                              maxlength="255"
                              placeholder="Ej. Pago de luz, compra en super…">{{ old('nota') }}</textarea>
                    @error('nota')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Un comentario breve para recordar el contexto.</div>
                        @enderror
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.flatpickr) {
                flatpickr('.js-date', {
                    dateFormat: 'd/m/Y',                 // mismo formato que el controlador
                    locale: 'es',
                    defaultDate: "{{ old('fecha', now()->format('d/m/Y')) }}",
                    maxDate: "{{ now()->format('d/m/Y') }}",
                    allowInput: true
                });
            }
        });
    </script>
@endsection
