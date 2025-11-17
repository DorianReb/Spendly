@extends('layouts.app')
@section('title','Detalle por categoría — Spendly')

@section('content')
    <style>
        .trx-cat-wrap{ max-width:800px; margin-inline:auto; padding:.5rem .25rem 1.5rem; }
        .trx-cat-topbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:.75rem; margin-bottom:.75rem;
        }
        .trx-cat-title{ font-weight:800; font-size:1.05rem; }
        .trx-cat-sub{ font-size:.9rem; color:#777; }
        .panel{
            background:#fff; border-radius:1rem; padding:1rem;
            box-shadow:0 10px 28px rgba(0,0,0,.06);
        }
        .item{
            background:rgba(0,0,0,.03);
            border-radius:.9rem; padding:.8rem .9rem;
            display:flex; align-items:center; gap:.75rem;
            margin-bottom:.5rem;
            text-decoration:none; color:inherit;
        }
        html[data-theme="dark"] .panel{ background:#1a171f; }
        html[data-theme="dark"] .item{ background:rgba(255,255,255,.03); }
        .item .icon{
            width:36px; height:36px; border-radius:50%; display:grid; place-items:center;
            color:#fff; flex-shrink:0;
        }
        .item .name{ font-weight:700; }
        .item .meta{ margin-left:auto; text-align:right; font-size:.9rem; color:#777; }
        .item .amount{ font-weight:800; color:inherit; }
    </style>

    <div class="trx-cat-wrap">
        <div class="trx-cat-topbar">
            <button class="btn btn-sm btn-outline-dark" onclick="history.back()">
                <i class="fa-solid fa-chevron-left me-1"></i> Atrás
            </button>
            <div class="text-end">
                <div class="trx-cat-title">{{ $categoria->nombre }}</div>
                <div class="trx-cat-sub">
                    {{ ucfirst($tipo) }} · Total: <strong>${{ number_format($total,2) }}</strong>
                </div>
            </div>
        </div>

        <section class="panel">
            @forelse($transacciones as $trx)
                <a href="{{ route('transacciones.show', $trx) }}" class="item">
                    <div class="icon"
                         style="background: {{ optional($trx->categoria)->color_hex ?? '#6C63FF' }}">
                        <i class="fa-solid {{ optional($trx->categoria)->icon ?? 'fa-circle' }}"></i>
                    </div>
                    <div>
                        <div class="name">{{ $trx->nota ?: 'Sin nota' }}</div>
                        <div class="text-muted" style="font-size:.85rem;">
                            {{ \Carbon\Carbon::parse($trx->fecha)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <div class="meta">
                        <div class="amount">
                            {{ $tipo === 'gasto' ? '-' : '+' }}${{ number_format($trx->importe,2) }}
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-muted mb-0">No hay transacciones en esta categoría aún.</p>
            @endforelse
        </section>
    </div>
@endsection
