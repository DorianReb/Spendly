<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraficoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $usuarioId = auth()->id();
        $hoy       = Carbon::today();

        // scope: dia | semana | mes | anio
        $scope = $request->query('scope', 'anio');

        // tipo de vista: general | gastos | ingresos
        $tipo  = $request->query('tipo', 'general');

        // Fecha de referencia (ref=YYYY-MM-DD), si no, hoy
        $ref = $request->query('ref')
            ? Carbon::parse($request->query('ref'))
            : $hoy->copy();

        // Resolver rango según scope
        [$inicio, $fin, $label, $prevRef, $nextRefCandidate] = $this->resolverRango($scope, $ref);

        // No permitir navegar a rangos futuros
        $nextRef = null;
        if ($nextRefCandidate && Carbon::parse($nextRefCandidate)->lte($hoy)) {
            $nextRef = $nextRefCandidate;
        }

        // Dataset global para GENERAL (ingresos / gastos / beneficio / pérdida)
        $data = $this->buildGraphicData($usuarioId, $inicio, $fin);

        // Categorías por tipo
        $catsGastos   = $this->categoriasDelPeriodo($usuarioId, $inicio, $fin, 'gastos');
        $catsIngresos = $this->categoriasDelPeriodo($usuarioId, $inicio, $fin, 'ingresos');

        // Categorías que se usan directamente en los tabs GASTOS / INGRESOS
        $categorias = [];
        if ($tipo === 'gastos') {
            $categorias = $catsGastos;
        } elseif ($tipo === 'ingresos') {
            $categorias = $catsIngresos;
        }

        return view('graficos.index', [
            'scope'        => $scope,
            'tipo'         => $tipo,
            'label'        => $label,
            'prevRef'      => $prevRef,
            'nextRef'      => $nextRef,
            'dataset'      => $data,
            'categorias'   => $categorias,
            'catsGastos'   => $catsGastos,
            'catsIngresos' => $catsIngresos,
            'inicioStr'    => $inicio->toDateString(),
            'finStr'       => $fin->toDateString(),
        ]);
    }

    /* -----------------------------------------
     * Resolver rango (día, semana, mes, año)
     * ----------------------------------------- */
    private function resolverRango(string $scope, Carbon $ref): array
    {
        switch ($scope) {

            case 'dia':
                $inicio = $ref->copy();
                $fin    = $ref->copy();
                return [
                    $inicio, $fin,
                    $ref->format('d/m/Y'),
                    $ref->copy()->subDay()->toDateString(),
                    $ref->copy()->addDay()->toDateString(),
                ];

            case 'semana':
                $inicio = $ref->copy()->startOfWeek();
                $fin    = $ref->copy()->endOfWeek();
                return [
                    $inicio, $fin,
                    'Semana del ' . $inicio->format('d/m'),
                    $ref->copy()->subWeek()->toDateString(),
                    $ref->copy()->addWeek()->toDateString(),
                ];

            case 'mes':
                $inicio = $ref->copy()->startOfMonth();
                $fin    = $ref->copy()->endOfMonth();
                return [
                    $inicio, $fin,
                    $ref->translatedFormat('F \\de Y'),
                    $ref->copy()->subMonth()->toDateString(),
                    $ref->copy()->addMonth()->toDateString(),
                ];

            case 'anio':
            default:
                $inicio = $ref->copy()->startOfYear();
                $fin    = $ref->copy()->endOfYear();
                return [
                    $inicio, $fin,
                    $ref->year,
                    $ref->copy()->subYear()->toDateString(),
                    $ref->copy()->addYear()->toDateString(),
                ];
        }
    }

    /* -----------------------------------------
     * Dataset del gráfico (totales globales)
     * ----------------------------------------- */
    private function buildGraphicData(int $usuarioId, Carbon $inicio, Carbon $fin): array
    {
        $inicioStr = $inicio->toDateString();
        $finStr    = $fin->toDateString();

        $ing = Transaccion::where('usuario_id', $usuarioId)
            ->where('tipo', 'ingreso')
            ->whereBetween('fecha', [$inicioStr, $finStr])
            ->sum('importe');

        $gas = Transaccion::where('usuario_id', $usuarioId)
            ->where('tipo', 'gasto')
            ->whereBetween('fecha', [$inicioStr, $finStr])
            ->sum('importe');

        $beneficio = $ing - $gas;
        $perdida   = $gas > $ing ? ($gas - $ing) : 0;

        return [
            'ingresos'  => $ing,
            'gastos'    => $gas,
            'beneficio' => max($beneficio, 0),
            'perdida'   => $perdida,
        ];
    }

    /* -----------------------------------------
     * Totales por categoría dentro del rango
     * $tipo: 'gastos' | 'ingresos'
     * ----------------------------------------- */
    private function categoriasDelPeriodo(
        int $usuarioId,
        Carbon $inicio,
        Carbon $fin,
        string $tipo
    ): array {
        $inicioStr = $inicio->toDateString();
        $finStr    = $fin->toDateString();

        $tipoTrx = $tipo === 'gastos' ? 'gasto' : 'ingreso';

        $query = Transaccion::with('categoria')
            ->where('usuario_id', $usuarioId)
            ->where('tipo', $tipoTrx)
            ->whereBetween('fecha', [$inicioStr, $finStr]);

        $items = $query->get()->groupBy('categoria_id');

        $result        = [];
        $totalPeriodo  = 0;

        // Primero calculamos totales por categoría
        foreach ($items as $categoriaId => $grupo) {
            $cat = $grupo->first()->categoria;
            if (!$cat) {
                continue;
            }

            $total = $grupo->sum('importe');
            $totalPeriodo += $total;

            $result[] = [
                'id'     => $cat->id,
                'nombre' => $cat->nombre,
                'icon'   => $cat->icon ?: 'fa-circle',
                'color'  => $cat->color_hex ?: '#6C63FF',
                'total'  => $total,
                // URL a lista de transacciones de esa categoría en el período
                'url'    => route('transacciones.porCategoria', [
                    'categoria' => $cat->id,
                    'tipo'      => $tipoTrx,
                    'desde'     => $inicioStr,
                    'hasta'     => $finStr,
                ]),
            ];
        }

        // Cálculo de porcentaje y orden
        foreach ($result as &$item) {
            $item['pct'] = $totalPeriodo > 0
                ? round($item['total'] * 100 / $totalPeriodo)
                : 0;
        }
        unset($item);

        usort($result, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $result;
    }
}
