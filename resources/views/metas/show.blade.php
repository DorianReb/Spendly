@extends('layouts.app')
@section('title','Detalle de meta — Spendly')

@section('content')
    <style>
        .meta-show-wrap{
            max-width: 700px;
            margin-inline:auto;
            padding:.75rem .25rem 1.5rem;
        }
        .meta-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:1rem;
        }
        .meta-title{ font-weight:800; font-size:1.1rem; }
        .card-soft{
            background:#fff; border-radius:1rem; padding:1rem 1.1rem 1.3rem;
            box-shadow:0 10px 25px rgba(0,0,0,.06); border:1px solid #f0f0f0;
        }
        html[data-theme="dark"] .card-soft{
            background:var(--card); /* Usamos la variable global más oscura */
            border-color: color-mix(in oklab, var(--card) 45%, var(--morado)); /* Borde sutil */
        }
        .badge-estado{
            padding:.25rem .65rem; border-radius:999px; font-size:.75rem;
            text-transform:uppercase; letter-spacing:.08em; font-weight:700;
        }

        /* 1. Modo Claro (Original) */
        .badge-en_curso{ background:rgba(108,99,255,.16); color:#6C63FF; }
        .badge-pausada{ background:#ffeeba; color:#856404; }
        .badge-completada{ background:#d4edda; color:#155724; }
        .badge-cancelada{ background:#f8d7da; color:#721c24; }

        /* 2. Modo Oscuro (Nuevos estilos) */
        html[data-theme="dark"] .badge-en_curso{ background:color-mix(in oklab, var(--morado) 20%, transparent); color:var(--morado); }
        html[data-theme="dark"] .badge-pausada{ background:rgba(233,198,110,.25); color:var(--amarillo); }
        html[data-theme="dark"] .badge-completada{ background:rgba(21,180,80,.25); color:#6be689; } /* Verde claro */
        html[data-theme="dark"] .badge-cancelada{ background:rgba(255,100,100,.25); color:#f56767; } /* Rojo claro */

        /* El resto de los estilos, como .progress, etc., se mantienen */
        .progress{ height:.7rem; border-radius:999px; overflow:hidden; background:#f1f3f5; }
        .progress{ height:.7rem; border-radius:999px; overflow:hidden; background:#f1f3f5; }
        .progress-bar{ background:linear-gradient(90deg, var(--morado), #9f8bff); }
        .aportes-list{ margin-top:1rem; }
        .aportes-item{
            display:flex; justify-content:space-between; align-items:center;
            padding:.45rem 0; border-bottom:1px solid #eee; font-size:.9rem;
        }
        html[data-theme="dark"] .aportes-item{ border-color:#2e2935; }

        /* --- Corrección de Legibilidad en Modo Oscuro --- */
        html[data-theme="dark"] .btn-dark-theme-fix.btn-outline-dark {
            border-color: var(--muted);
            color: var(--muted);
            background: transparent;
        }
        html[data-theme="dark"] .btn-dark-theme-fix.btn-outline-dark:hover {
            border-color: var(--morado);
            color: var(--morado);
            background: color-mix(in oklab, var(--morado) 12%, transparent);
        }
        html[data-theme="dark"] .btn-dark-theme-fix.btn-outline-primary {
            border-color: var(--morado);
            color: var(--morado);
            background: transparent;
        }
        html[data-theme="dark"] .btn-dark-theme-fix.btn-outline-primary:hover {
            color: var(--card); /* Para que el texto sea oscuro sobre el fondo morado en hover */
            background: var(--morado);
            border-color: var(--morado);
        }
        html[data-theme="dark"] .text-muted {
            color: var(--muted) !important;
        }
    </style>

    <div class="meta-show-wrap">
        {{-- Topbar --}}
        <div class="meta-topbar">
            <a href="{{ route('metas.index') }}" class="btn btn-sm btn-outline-dark btn-dark-theme-fix">
                <i class="fa-solid fa-chevron-left me-1"></i> Volver
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('metas.edit', $meta) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                </a>
                <form method="POST" action="{{ route('metas.destroy', $meta) }}" id="formDeleteMeta">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeleteMeta">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Detalle meta --}}
        <section class="card-soft mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h1 class="meta-title mb-1">{{ $meta->nombre }}</h1>
                    <div class="text-muted small">
                        Objetivo: ${{ number_format($meta->objetivo, 2) }}
                        @if($meta->fecha_limite)
                            · Límite {{ \Carbon\Carbon::parse($meta->fecha_limite)->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
                <span class="badge-estado badge-{{ $meta->estado }}">
                    {{ strtoupper(str_replace('_',' ',$meta->estado)) }}
                </span>
            </div>

            {{-- Progreso --}}
            <div class="mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Progreso</span>
                    <span>{{ $porcentaje }}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $porcentaje }}%;"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-2 small text-muted">
                <span>Aportado: <strong>${{ number_format($totalAportado, 2) }}</strong></span>
                <span>Restante:
                    <strong>${{ number_format(max($meta->objetivo - $totalAportado, 0), 2) }}</strong>
                </span>
            </div>
        </section>

        {{-- Aportes --}}
        <section class="card-soft">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 mb-0">Aportes registrados</h2>
                <button type="button"
                        class="btn btn-sm btn-outline-primary btn-dark-theme-fix"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAporte">
                    <i class="fa-solid fa-plus me-1"></i> Añadir aporte
                </button>

            </div>

            @if($aportes->isEmpty())
                <p class="text-muted small mb-0">
                    Todavía no has registrado aportes en esta meta.
                </p>
            @else
                <div class="aportes-list">
                    @foreach($aportes as $aporte)
                        <div class="aportes-item">
                            <div>
                                <div class="fw-semibold">
                                    ${{ number_format($aporte->monto, 2) }}
                                </div>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($aporte->fecha)->format('d/m/Y') }}
                                    @if($aporte->nota)
                                        · {{ $aporte->nota }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnDelete = document.getElementById('btnDeleteMeta');
            const formDelete = document.getElementById('formDeleteMeta');

            if (btnDelete && formDelete) {
                btnDelete.addEventListener('click', () => {
                    Swal.fire({
                        title: '¿Eliminar meta?',
                        text: 'Esta acción eliminará la meta y sus aportes asociados.',
                        icon: 'warning',
                        showCancelButton: true,
                        // Eliminamos los colores fijos para que SweetAlert2 los herede
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formDelete.submit();
                        }
                    });
                });
            }
        });
    </script>
    {{-- Modal: Nuevo aporte --}}
    <div class="modal fade" id="modalAporte" tabindex="-1" aria-labelledby="modalAporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('metas.aportes.store', $meta) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAporteLabel">
                            Nuevo aporte a: {{ $meta->nombre }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Monto --}}
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="monto"
                                       id="monto"
                                       step="0.01"
                                       min="0.01"
                                       class="form-control"
                                       required
                                       placeholder="Ej. 500.00">
                            </div>
                            <div class="form-text small">
                                Cantidad que deseas aportar a esta meta.
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="mb-3">
                            <label for="fecha_aporte" class="form-label">Fecha</label>
                            <input type="text"
                                    name="fecha"
                                    id="fecha_aporte"
                                    class="form-control js-aporte-date"
                                    value="{{ now()->format('d/m/Y') }}"
                                    required
                                    autocomplete="off">
                            <div class="form-text small">
                                No puede ser una fecha futura.
                            </div>
                        </div>

                        {{-- Nota --}}
                        <div class="mb-3">
                            <label for="nota_aporte" class="form-label">Nota (opcional)</label>
                            <textarea name="nota"
                                      id="nota_aporte"
                                      rows="2"
                                      class="form-control"
                                      maxlength="255"
                                      placeholder="Ej. Ahorro de quincena, venta de cosas, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Guardar aporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // SweetAlert para eliminar meta (esto ya lo tenías)
            const btnDelete = document.getElementById('btnDeleteMeta');
            const formDelete = document.getElementById('formDeleteMeta');

            if (btnDelete && formDelete) {
                btnDelete.addEventListener('click', () => {
                    Swal.fire({
                        title: '¿Eliminar meta?',
                        text: 'Esta acción eliminará la meta y sus aportes asociados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formDelete.submit();
                        }
                    });
                });
            }

            // Flatpickr para la fecha del aporte (sin fechas futuras)
            if (window.flatpickr) {
                flatpickr('.js-aporte-date', {
                    dateFormat: 'd/m/Y', // mismo formato que usa parseFecha
                    maxDate: '{{ now()->format('d/m/Y') }}',
                    defaultDate: '{{ now()->format('d/m/Y') }}',
                    locale: 'es',
                    allowInput: true
                });
            }
        });
    </script>

@endsection
