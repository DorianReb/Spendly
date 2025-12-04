@extends('layouts.app')
@section('title','Metas — Spendly')

@section('content')
    <style>
        .metas-wrap{
            max-width: 1000px;
            margin-inline:auto;
            padding: .75rem .25rem 1.5rem;
        }
        .metas-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:1rem;
        }
        .metas-title{ font-weight:800; font-size:1.2rem; letter-spacing:.02em; }
        .metas-sub{ color:var(--muted); font-size:.9rem; }
        /* .card-meta y .badge-estado ya son globales (.card-soft) */

        .chip-fecha{
            font-size:.8rem;
            color:var(--muted);
        }
        .meta-card-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:.75rem;
        }
        .meta-name{
            font-weight:700;
            font-size:1rem;
        }
        .dot-color{
            width:14px; height:14px; border-radius:50%;
            display:inline-block; margin-right:.4rem;
            border:1px solid rgba(0,0,0,.08);
        }
        /* Corrección de borde del punto de color en modo oscuro */
        html[data-theme="dark"] .dot-color {
            border-color: color-mix(in oklab, var(--card) 45%, var(--morado));
        }

        .progress{ height:.6rem; border-radius:999px; overflow:hidden; background:#f1f3f5; }
        .progress-bar{
            background:linear-gradient(90deg, var(--morado), #9f8bff);
        }
        .meta-footer{
            display:flex; justify-content:space-between; align-items:center;
            font-size:.85rem;
            color:var(--muted);
            gap:.5rem;
            flex-wrap:wrap;
        }
        .btn-sm-soft{
            border-radius:999px; font-size:.8rem; padding:.25rem .6rem;
        }
    </style>

    <div class="metas-wrap">
        {{-- Topbar --}}
        <div class="metas-topbar">
            <div>
                <div class="metas-title">Mis metas de ahorro</div>
                <div class="metas-sub">
                    Visualiza tu progreso y registra aportes a tus objetivos financieros.
                </div>
            </div>
            <a href="{{ route('metas.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Nueva meta
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($metas->isEmpty())
            <div class="card-meta">
                <p class="mb-1 fw-semibold">Aún no has creado metas.</p>
                <p class="mb-2 text-muted small">
                    Crea tu primera meta de ahorro para empezar a registrar aportes y medir tu progreso.
                </p>
                <a href="{{ route('metas.create') }}" class="btn btn-sm btn-primary">
                    Crear meta
                </a>
            </div>
        @else
            <div class="row g-3">
                @foreach($metas as $meta)
                    @php
                        $pct = $meta->porcentaje ?? 0;
                        $pct = min(100, max(0, $pct));
                        $color = $meta->color_hex ?? '#6C63FF'; // si tu tabla no tiene color_hex, puedes quitar esto
                    @endphp
                    <div class="col-12 col-md-6">
                        <div class="card-soft h-100">
                            <div class="meta-card-header mb-2">
                                <div>
                                    <div class="meta-name">
                                        <span class="dot-color" style="background:{{ $color }}"></span>
                                        {{ $meta->nombre }}
                                    </div>
                                    <div class="chip-fecha">
                                        Objetivo: ${{ number_format($meta->objetivo,2) }}
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
                                    <span>Aportado</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>

                            <div class="meta-footer mt-2">
                                <div>
                                    <span class="fw-semibold">
                                        ${{ number_format($meta->total_aportado ?? 0, 2) }}
                                    </span>
                                    de ${{ number_format($meta->objetivo,2) }}
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('metas.show', $meta) }}"
                                       class="btn btn-outline-secondary btn-sm btn-sm-soft">
                                        Ver
                                    </a>
                                    <a href="{{ route('metas.edit', $meta) }}"
                                       class="btn btn-outline-primary btn-sm btn-sm-soft">
                                        Editar
                                    </a>
                                    <form action="{{ route('metas.destroy', $meta) }}"
                                          method="POST" class="d-inline form-delete-meta">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm btn-sm-soft btn-delete-meta">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SweetAlert2 para eliminación --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-delete-meta').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const form = btn.closest('form');

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
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
