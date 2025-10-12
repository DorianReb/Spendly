@extends('layouts.app')
@section('title','Inicio')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
        }
        body{ background: var(--beige); color: var(--gris); }
        .screen{ max-width: 980px; margin: 0 auto; padding: clamp(16px, 3vw, 24px); }
        .card-soft{ background:#fff; border:1px solid #eee; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,.06); }
        .kpi{ padding:18px; border-radius:16px; border:1px solid #f0f0f0; }
        .badge-trend{ background:var(--amarillo); color:#3a3220; font-weight:700; border-radius:999px; padding:.35rem .7rem; }
        .btn-primary{ background:var(--morado); border:none; box-shadow:0 10px 22px rgba(108,99,255,.25); }
        .btn-primary:hover{ background:#584efc; }
        .btn-ghost{ border:1px solid #ddd; background:#fff; color: var(--gris); }
        .divider{ height:1px; background: #eee; margin: 12px 0; }
        .list-item{ display:flex; justify-content:space-between; align-items:center; padding:12px 0; }
        .list-item + .list-item{ border-top:1px solid #f1f1f1; }
        .amount-pos{ color:#198754; font-weight:700; }
        .amount-neg{ color:#dc3545; font-weight:700; }
        .chip{ display:inline-flex; align-items:center; gap:6px; padding:.35rem .6rem; border-radius:999px; font-size:.8rem; background: #f7f7fb; border:1px solid #ededf6; }
        .fab{
            position: fixed; right: 18px; bottom: 18px; width:58px; height:58px; border-radius:50%;
            background: var(--morado); color:#fff; display:grid; place-items:center; border:none;
            box-shadow: 0 14px 28px rgba(108,99,255,.35); z-index: 1040;
        }
        .hello{ font-weight:800; line-height:1.1; }
        .muted{ color:#555; }
    </style>

    <main class="screen">
        {{-- Encabezado / saludo --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="muted small">Hola, {{ auth()->user()->nombre ?? 'Invitado' }}</div>
                <h1 class="hello h3 m-0">Tu resumen financiero</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url('/reportes') }}" class="btn btn-ghost btn-sm">Reportes</a>
                @auth
                    <a href="{{ url('/logout') }}" class="btn btn-ghost btn-sm">Salir</a>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-primary btn-sm">Entrar</a>
                @endauth
            </div>
        </div>

        {{-- Balance + tendencia --}}
        <div class="card-soft p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="muted small">Balance (Mes actual)</div>
                    <div class="h2 m-0" style="color:var(--gris);">
                        {{-- Ejemplo: {{$balanceMensual}} --}}
                        $12,540.00
                    </div>
                </div>
                <span class="badge-trend">+2.4% vs mes pasado</span>
            </div>

            {{-- Filtros rápidos --}}
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <button class="chip">Mes</button>
                <button class="chip">Semana</button>
                <button class="chip">Hoy</button>
                <button class="chip">Personalizado</button>
            </div>
        </div>

        {{-- KPIs Ingresos / Gastos --}}
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="kpi">
                    <div class="muted small">Ingresos</div>
                    <div class="fs-5 fw-semibold">$18,250.00</div>
                </div>
            </div>
            <div class="col-6">
                <div class="kpi">
                    <div class="muted small">Gastos</div>
                    <div class="fs-5 fw-semibold">$5,710.00</div>
                </div>
            </div>
        </div>

        {{-- Meta destacada --}}
        <div class="card-soft p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="muted small">Meta activa</div>
                    <div class="fw-semibold">Fondo de emergencia · 65% completado</div>
                </div>
                <a href="{{ url('/metas') }}" class="btn btn-ghost btn-sm">Ver metas</a>
            </div>
        </div>

        {{-- Movimientos recientes --}}
        <div class="card-soft p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h2 class="h6 m-0">Movimientos recientes</h2>
                <a href="{{ url('/transacciones') }}" class="btn btn-ghost btn-sm">Ver todo</a>
            </div>
            <div class="divider"></div>

            {{-- Item --}}
            <div class="list-item">
                <div>
                    <div class="fw-semibold">Salario</div>
                    <div class="small muted">Ingreso · 02/10 · Nómina</div>
                </div>
                <div class="amount-pos">+ $12,000.00</div>
            </div>

            <div class="list-item">
                <div>
                    <div class="fw-semibold">Supermercado</div>
                    <div class="small muted">Gasto · 03/10 · Hogar</div>
                </div>
                <div class="amount-neg">− $820.00</div>
            </div>

            <div class="list-item">
                <div>
                    <div class="fw-semibold">Transporte</div>
                    <div class="small muted">Gasto · 04/10 · Taxi</div>
                </div>
                <div class="amount-neg">− $135.00</div>
            </div>
        </div>
    </main>

    {{-- FAB para agregar --}}
    <a href="{{ url('/transacciones/create') }}" class="fab" aria-label="Agregar movimiento">＋</a>
@endsection
