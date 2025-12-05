@extends('layouts.app')
@section('title','Consejos Financieros — Spendly')

@section('content')
    <style>
        .page-wrap{ max-width: 900px; margin-inline:auto; padding: var(--pad-2); }
        .tip-card{
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }
        .tip-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--morado);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .tip-card p {
            font-size: var(--fs-0);
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }
        .citation-box {
            font-size: 0.8rem;
            margin-top: 2rem;
            padding: 1rem;
            border-left: 4px solid var(--amarillo);
            background: color-mix(in oklab, var(--bg) 85%, var(--card));
            color: var(--muted);
            border-radius: 0 0.5rem 0.5rem 0;
        }
        html[data-theme="dark"] .citation-box {
            background: color-mix(in oklab, var(--card) 95%, transparent);
            border-left-color: var(--amarillo);
        }
    </style>

    <div class="page-wrap">
        <header class="mb-5">
            <h1 class="text-3xl font-extrabold mb-1" style="font-size:2rem; font-weight:800;">
                Consejos Esenciales para el Bienestar Financiero
            </h1>
            <p class="text-muted" style="font-size:1rem;">
                La planificación y el orden son herramientas fundamentales para tomar el control de tus finanzas personales.
            </p>
        </header>

        <div class="tip-card card-soft">
            <h3><i class="fa-solid fa-bullseye"></i> Haz un Presupuesto y Conócelo</h3>
            <p>
                El éxito financiero no depende únicamente de cuánto ingresas, sino de la calidad de tus decisiones. Un presupuesto permite identificar con claridad <strong>dónde va tu dinero</strong> y cómo optimizar tus gastos.
            </p>
            <p class="text-muted small mt-2">
                <strong>Acción:</strong> Registra tus ingresos y clasifica tus gastos en fijos, variables necesarios y discrecionales. Cumplir tu presupuesto es el paso más importante.
            </p>
        </div>

        <div class="tip-card card-soft">
            <h3><i class="fa-solid fa-piggy-bank"></i> Ahorra Primero, Gasta Después (Regla 50/30/20)</h3>
            <p>
                Considera el ahorro como un compromiso contigo mismo, no como lo que sobra al final del mes. La regla 50/30/20 es una guía efectiva para distribuir tus ingresos:
            </p>
            <ul>
                <li><strong>50%</strong> para <strong>Necesidades</strong> (vivienda, alimentos, servicios).</li>
                <li><strong>30%</strong> para <strong>Deseos</strong> (ocio y gastos personales).</li>
                <li><strong>20%</strong> para <strong>Ahorro e Inversión</strong>.</li>
            </ul>
            <p class="text-muted small mt-2">
                <strong>Acción:</strong> Automatiza el ahorro mensual para garantizar disciplina y constancia.
            </p>
        </div>

        <div class="tip-card card-soft">
            <h3><i class="fa-solid fa-shield-halved"></i> Crea un Fondo de Emergencia</h3>
            <p>
                Un fondo de emergencia protege tus finanzas ante imprevistos y evita recurrir a deudas costosas durante momentos críticos.
            </p>
            <p class="text-muted small mt-2">
                <strong>Objetivo:</strong> Acumula entre <strong>3 y 6 meses</strong> de tus gastos esenciales.
            </p>
        </div>

        <div class="tip-card card-soft">
            <h3><i class="fa-solid fa-credit-card"></i> Domina el Crédito, Evita la Deuda</h3>
            <p>
                El crédito es una herramienta útil si se administra correctamente. Acumular deudas de alto interés puede afectar gravemente tu estabilidad financiera.
            </p>
            <p class="text-muted small mt-2">
                <strong>Acción:</strong> Procura ser <strong>totalero</strong> (liquidar tu saldo completo cada mes). Si tienes varias deudas, liquida primero las que generan más intereses.
            </p>
        </div>

        <div class="tip-card card-soft">
            <h3><i class="fa-solid fa-chart-line"></i> Empieza a Invertir a Largo Plazo</h3>
            <p>
                Invertir permite que tu dinero crezca con el tiempo y supere la inflación. Entre más pronto comiences, mejores serán los resultados a largo plazo.
            </p>
            <p class="text-muted small mt-2">
                <strong>Acción:</strong> Comienza con instrumentos accesibles como fondos indexados o CETES. Recuerda: la clave es la <strong>diversificación</strong> y la constancia.
            </p>
        </div>

        <div class="citation-box">
            <strong>Fuentes y Referencias:</strong>
            <p class="mb-0">
                Basado en guías financieras de la Oficina para la Protección Financiera del Consumidor (CFPB), DFPI, BBVA y el Instituto Tecnológico de Monterrey (ITESM).
            </p>
        </div>

    </div>
@endsection
