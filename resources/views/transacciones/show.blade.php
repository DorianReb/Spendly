@extends('layouts.app')
@section('title','Detalle de transacción — Spendly')

@section('content')
    <style>
        .trx-show-wrap{ max-width:600px; margin-inline:auto; padding:.5rem .25rem 1.5rem; }
        .trx-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:.75rem;
        }
        .trx-title{ font-weight:800; font-size:1.05rem; }
        .panel{
            background:#fff; border-radius:1rem; padding:1rem 1.1rem 1.3rem;
            box-shadow:0 10px 28px rgba(0,0,0,.06);
        }
        html[data-theme="dark"] .panel{ background:#1a171f; }
        .pill-tipo{
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.25rem .7rem; border-radius:999px;
            font-size:.8rem; font-weight:700; letter-spacing:.06em;
            text-transform:uppercase;
            background: rgba(108,99,255,.12);
            color:#6C63FF;
        }
        .amount-big{
            font-size:2rem; font-weight:800;
        }
    </style>

    <div class="trx-show-wrap">
        <div class="trx-topbar">
            <button class="btn btn-sm btn-outline-dark" onclick="history.back()">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </button>
            <div class="trx-title">Transacción</div>
            <a href="{{ route('transacciones.edit', $transaccion) }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-pen me-1"></i> Editar
            </a>
        </div>

        <section class="panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="pill-tipo">
                    @if($transaccion->tipo === 'gasto')
                        <i class="fa-solid fa-arrow-trend-down"></i> Gasto
                    @else
                        <i class="fa-solid fa-arrow-trend-up"></i> Ingreso
                    @endif
                </span>
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($transaccion->fecha)->translatedFormat('d M Y') }}
                </small>
            </div>

            <div class="amount-big mb-2">
                {{ $transaccion->tipo === 'gasto' ? '-' : '+' }}${{ number_format($transaccion->importe,2) }}
            </div>

            <p class="mb-1">
                <strong>Categoría:</strong>
                {{ optional($transaccion->categoria)->nombre ?? 'Sin categoría' }}
            </p>

            @if($transaccion->nota)
                <p class="mb-1"><strong>Nota:</strong> {{ $transaccion->nota }}</p>
            @endif

            <hr>

            <div class="d-flex justify-content-between gap-2">
                <form id="formDeleteTrx" method="POST" action="{{ route('transacciones.destroy', $transaccion) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="btnDeleteTrx" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar
                    </button>
                </form>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                    Volver al inicio
                </a>
            </div>
        </section>
    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnDelete = document.getElementById('btnDeleteTrx');
            const formDelete = document.getElementById('formDeleteTrx');

            if (btnDelete && formDelete) {
                btnDelete.addEventListener('click', () => {
                    Swal.fire({
                        title: '¿Eliminar transacción?',
                        text: 'Esta acción no se puede deshacer.',
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
        });
    </script>
@endsection
