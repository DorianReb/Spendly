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
        .meta-name{ font-weight:700; font-size:1rem; }
        .dot-color{
            width:14px; height:14px; border-radius:50%;
            display:inline-block; margin-right:.4rem;
            border:1px solid rgba(0,0,0,.08);
        }
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

        .section-title{
            margin-top:1.5rem;
            margin-bottom:.75rem;
            font-weight:800;
            font-size:1.05rem;
            color:var(--text);
        }
    </style>

    <div class="metas-wrap">

        {{-- Topbar --}}
        <div class="metas-topbar">
            <div>
                <div class="metas-title">Mis metas de ahorro</div>
                <div class="metas-sub">
                    Visualiza tu progreso y registra aportes.
                </div>
            </div>
            <a href="{{ route('metas.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Nueva meta
            </a>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        {{-- ============================== --}}
        {{--        METAS ACTIVAS            --}}
        {{-- ============================== --}}
        <div class="section-title">Metas activas</div>

        @if($metasActivas->isEmpty())
            <div class="card-soft p-3 mb-3">
                <p class="fw-semibold mb-1">No tienes metas activas.</p>
                <p class="text-muted small mb-2">Crea una meta nueva para comenzar.</p>
                <a href="{{ route('metas.create') }}" class="btn btn-sm btn-primary">Crear meta</a>
            </div>
        @else
            <div class="row g-3">
                @foreach($metasActivas as $meta)
                    @include('metas.card', ['meta' => $meta])
                @endforeach
            </div>
        @endif


        {{-- ============================== --}}
        {{--       METAS COMPLETADAS        --}}
        {{-- ============================== --}}
        <div class="section-title mt-4">Metas completadas</div>

        @if($metasCompletadas->isEmpty())
            <p class="text-muted small">Aún no has completado ninguna meta.</p>
        @else
            <div class="row g-3">
                @foreach($metasCompletadas as $meta)
                    @include('metas.card', ['meta' => $meta])
                @endforeach
            </div>
        @endif

    </div>

    {{-- SweetAlert2 para eliminación --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-delete-meta').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');

                    Swal.fire({
                        title: '¿Eliminar meta?',
                        text: 'Esta acción eliminará la meta y sus aportes asociados.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        theme: document.documentElement.getAttribute('data-theme') === 'dark'
                            ? 'dark'
                            : undefined
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endsection
