<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\AporteMeta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de metas del usuario.
     * En las vistas puedes usar:
     *  - $meta->total_aportado
     *  - $meta->porcentaje
     *  - $meta->restante
     */
    public function index()
    {
        $usuarioId = auth()->id();

        $metas = Meta::where('usuario_id', $usuarioId)
            ->with('aportes') // para evitar N+1 (opcional pero recomendable)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('metas.index', compact('metas'));
    }

    /**
     * Formulario para crear una meta nueva
     */
    public function create()
    {
        return view('metas.create');
    }

    /**
     * Detalle de una meta + aportes
     */
    public function show(Meta $meta)
    {
        // Asegurar que la meta es del usuario autenticado
        $this->authorizeMeta($meta);

        // Traer aportes relacionados
        $meta->load(['aportes' => function($q) {
            $q->orderBy('fecha', 'desc');
        }]);

        // Usamos los accessors del modelo
        $totalAportado = $meta->total_aportado;
        $porcentaje    = $meta->porcentaje;

        return view('metas.show', [
            'meta'          => $meta,
            'aportes'       => $meta->aportes,
            'totalAportado' => $totalAportado,
            'porcentaje'    => $porcentaje,
        ]);
    }

    /**
     * Guarda una meta nueva
     */
    public function store(Request $request)
    {
        $usuarioId = auth()->id();

        // 1) Tomar el valor “real” del objetivo
        //    - Primero intenta con el hidden (name="objetivo")
        //    - Si por X razón viniera sólo del visible (objetivo_mask), también lo tomamos
        $rawObjetivo = $request->input('objetivo', $request->input('objetivo_mask'));

        if ($rawObjetivo !== null) {
            // Quitar comas y espacios: "10,000.50" -> "10000.50"
            $rawObjetivo = str_replace([',', ' '], '', $rawObjetivo);
        }

        // 2) Validar
        $validated = $request->validate([
            'nombre'                  => 'required|string|max:100',
            'fecha_limite'            => 'nullable|date|after:today',
            'aporte_mensual_sugerido' => 'nullable|numeric|min:0',
            'estado'                  => 'nullable|in:en_curso,completada,pausada,cancelada',
        ]);

        // Si no hay objetivo válido, lanzamos error de validación manualmente
        if ($rawObjetivo === null || $rawObjetivo === '' || !is_numeric($rawObjetivo) || $rawObjetivo <= 0) {
            return back()
                ->withErrors(['objetivo' => 'El monto objetivo es obligatorio y debe ser mayor a 0.'])
                ->withInput();
        }

        // 3) Armar el arreglo final para create()
        $data = $validated;
        $data['usuario_id'] = $usuarioId;
        $data['objetivo']   = $rawObjetivo;   // 👈 se guarda en la columna correcta

        // Si no viene estado, dejamos que la BD use su default (en_curso)
        if (empty($data['estado'])) {
            unset($data['estado']);
        }

        Meta::create($data);

        return redirect()
            ->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    /**
     * Editar meta
     */
    public function edit(Meta $meta)
    {
        $this->authorizeMeta($meta);

        return view('metas.edit', compact('meta'));
    }

    /**
     * Actualizar meta
     */
    public function update(Request $request, Meta $meta)
    {
        $this->authorizeMeta($meta);

        $data = $request->validate([
            'nombre'        => 'required|string|max:140',
            'objetivo'      => 'required|numeric|min:0.01',
            'fecha_limite'  => 'nullable|date',
            'estado'        => 'required|in:en_curso,pausada,completada,cancelada',
            'aporte_mensual_sugerido' => 'nullable|numeric|min:0',
        ]);

        $meta->update($data);

        return redirect()
            ->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    /**
     * Eliminar (soft delete) una meta
     */
    public function destroy(Meta $meta)
    {
        $this->authorizeMeta($meta);

        $meta->delete();

        return redirect()
            ->route('metas.index')
            ->with('success', 'Meta eliminada correctamente.');
    }

    /**
     * Registrar un aporte a una meta
     * Actualiza automáticamente el estado si llega al 100%.
     */
    public function storeAporte(Request $request, Meta $meta)
    {
        $this->authorizeMeta($meta);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date|before_or_equal:today',
            'nota'  => 'nullable|string|max:255',
        ]);

        AporteMeta::create([
            'meta_id'        => $meta->id,
            'usuario_id'     => auth()->id(),
            'transaccion_id' => null,      // si luego la vinculas a una transacción, lo llenas
            'monto'          => $data['monto'],
            'fecha'          => $data['fecha'],
            'nota'           => $data['nota'] ?? null,
        ]);

        // 🔄 Recalcular progreso y, si aplica, marcar completada
        $meta->refresh();

        if ($meta->porcentaje >= 100 && $meta->estado === 'en_curso') {
            $meta->estado = 'completada';
            $meta->save();
        }

        return redirect()
            ->route('metas.show', $meta)
            ->with('success', 'Aporte registrado correctamente.');
    }

    /**
     * Verificación simple de que la meta pertenece al usuario logueado
     */
    protected function authorizeMeta(Meta $meta)
    {
        if ($meta->usuario_id !== auth()->id()) {
            abort(403);
        }
    }
}
