<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $usuarioId = auth()->id();
        $hoy       = Carbon::today();

        // pestaña actual: gastos / ingresos
        $tipoTab = $request->query('tipo', 'gasto');   // 'gasto' | 'ingreso'
        // rango actual: dia / semana / mes / anio / periodo
        $scope   = $request->query('scope', 'dia');

        // --- calcular fechas inicio/fin, etiqueta y referencias prev/next ---
        [$inicio, $fin, $rangeLabel, $prevRef, $nextRefCandidate] =
            $this->resolverRango($scope, $request, $hoy);

        // datasets reales para gastos e ingresos (SOLO en el rango elegido)
        $dataGastos   = $this->buildDataset($usuarioId, 'gasto',   $inicio, $fin);
        $dataIngresos = $this->buildDataset($usuarioId, 'ingreso', $inicio, $fin);

        $datasets = [
            'gastos'   => $dataGastos,
            'ingresos' => $dataIngresos,
        ];

        // === BALANCE TOTAL HISTÓRICO (ingresos – gastos, sin filtrar fechas) ===
        $totalGastosGlobal = Transaccion::where('usuario_id', $usuarioId)
            ->where('tipo', 'gasto')
            ->sum('importe');

        $totalIngresosGlobal = Transaccion::where('usuario_id', $usuarioId)
            ->where('tipo', 'ingreso')
            ->sum('importe');

        $balance = $totalIngresosGlobal - $totalGastosGlobal;
        $balanceFormatted = '$' . number_format($balance, 2);

        // === URLs prev/next sin permitir ir al futuro ===
        $prevUrl = null;
        $nextUrl = null;

        if ($prevRef) {
            $prevUrl = route('home', [
                'tipo'  => $tipoTab,
                'scope' => $scope,
                'ref'   => $prevRef,
            ]);
        }

        if ($nextRefCandidate && Carbon::parse($nextRefCandidate)->lte($hoy)) {
            $nextUrl = route('home', [
                'tipo'  => $tipoTab,
                'scope' => $scope,
                'ref'   => $nextRefCandidate,
            ]);
        }

        // Fechas para el formulario de “Período”
        $desdePeriodo = $scope === 'periodo' ? $inicio->toDateString() : null;
        $hastaPeriodo = $scope === 'periodo' ? $fin->toDateString()    : null;

        return view('home', [
            'tipoTab'          => $tipoTab,
            'scope'            => $scope,
            'rangeLabel'       => $rangeLabel,
            'balanceFormatted' => $balanceFormatted,
            'datasets'         => $datasets,
            'prevUrl'          => $prevUrl,
            'nextUrl'          => $nextUrl,
            'desdePeriodo'     => $desdePeriodo,
            'hastaPeriodo'     => $hastaPeriodo,
        ]);
    }

    /**
     * Calcula inicio/fin del rango, etiqueta y referencias prev/next
     * usando una fecha de referencia (?ref=YYYY-MM-DD).
     */
    protected function resolverRango(string $scope, Request $request, Carbon $hoy): array
    {
        $refStr = $request->query('ref');
        $ref    = $refStr ? Carbon::parse($refStr) : $hoy->copy();

        switch ($scope) {
            case 'semana':
                $inicio = $ref->copy()->startOfWeek();
                $fin    = $ref->copy()->endOfWeek();
                $label  = $inicio->format('d M') . ' – ' . $fin->format('d M');

                $prevRef = $ref->copy()->subWeek()->toDateString();
                $nextRef = $ref->copy()->addWeek()->toDateString();
                break;

            case 'mes':
                $inicio = $ref->copy()->startOfMonth();
                $fin    = $ref->copy()->endOfMonth();
                $label  = $ref->translatedFormat('F \\de Y');

                $prevRef = $ref->copy()->subMonth()->toDateString();
                $nextRef = $ref->copy()->addMonth()->toDateString();
                break;

            case 'anio':
                $inicio = $ref->copy()->startOfYear();
                $fin    = $ref->copy()->endOfYear();
                $label  = $ref->year;

                $prevRef = $ref->copy()->subYear()->toDateString();
                $nextRef = $ref->copy()->addYear()->toDateString();
                break;

            case 'periodo':
                $desdeStr = $request->query('desde');
                $hastaStr = $request->query('hasta');

                if ($desdeStr && $hastaStr) {
                    $inicio = Carbon::parse($desdeStr);
                    $fin    = Carbon::parse($hastaStr);
                } else {
                    $inicio = $hoy->copy()->startOfYear();
                    $fin    = $hoy->copy();
                }

                $label = $inicio->format('d M Y') . ' – ' . $fin->format('d M Y');

                // en “Período” no tiene mucho sentido prev/next
                return [$inicio, $fin, $label, null, null];

            case 'dia':
            default:
                $inicio = $ref->copy();
                $fin    = $ref->copy();

                if ($ref->isSameDay($hoy)) {
                    $label = 'Hoy, ' . $ref->translatedFormat('d \\de F');
                } else {
                    $label = $ref->translatedFormat('d \\de F');
                }

                $prevRef = $ref->copy()->subDay()->toDateString();
                $nextRef = $ref->copy()->addDay()->toDateString();
                break;
        }

        return [$inicio, $fin, $label, $prevRef, $nextRef];
    }

    /**
     * Construye los datos para el donut/lista de una pestaña (gastos o ingresos)
     */
    protected function buildDataset(int $usuarioId, string $tipo, Carbon $inicio, Carbon $fin): array
    {
        $transacciones = Transaccion::with('categoria')
            ->where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->get();

        $grouped = $transacciones->groupBy('categoria_id');

        $items = [];
        $total = 0;

        foreach ($grouped as $categoriaId => $grupo) {
            $cat   = $grupo->first()->categoria;
            $suma  = $grupo->sum('importe');
            $total += $suma;

            $items[] = [
                'categoria_id' => $categoriaId,
                'name'         => $cat->nombre,
                'icon'         => $cat->icon ?: 'fa-circle',
                'color'        => $cat->color_hex ?: '#6C63FF',
                'raw_total'    => $suma,
            ];
        }

        // porcentajes numéricos (para el conic-gradient)
        foreach ($items as &$it) {
            $pct       = $total > 0 ? round($it['raw_total'] * 100 / $total) : 0;
            $it['pct'] = $pct; // solo número
            $it['amount'] = '$' . number_format($it['raw_total'], 2);
        }
        unset($it);

        // ordenar por total desc
        usort($items, fn($a, $b) => $b['raw_total'] <=> $a['raw_total']);

        // ya no necesitamos raw_total en el front
        foreach ($items as &$it) {
            unset($it['raw_total']);
        }
        unset($it);

        return [
            'center' => '$' . number_format($total, 2),
            'total'  => $total,
            'items'  => $items,
        ];
    }
}
