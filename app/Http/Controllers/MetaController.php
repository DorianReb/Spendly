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

    /* ---------------------------------------------------------
     |   MÉTODO PRIVADO PARA PARSEAR FECHAS dd/mm/yy → Y-m-d
     ----------------------------------------------------------*/
    private function parseFecha($fecha)
    {
        if (!$fecha) return null;

        // dd/mm/yyyy
        if (str_contains($fecha, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
            } catch (\Exception $e) {}

            // dd/mm/yy
            try {
                return Carbon::createFromFormat('d/m/y', $fecha)->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        // Si viene en formato estándar
        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Listado de metas del usuario.
     * Separadas entre activas y completadas.
     */
    public function index()
    {
        $usuarioId = auth()->id();

        // Metas activas (TODO menos completadas)
        $metasActivas = Meta::where('usuario_id', $usuarioId)
            ->where('estado', '!=', 'completada')
            ->with('aportes')
            ->orderBy('created_at', 'desc')
            ->get();

        // Metas completadas enviadas al final
        $metasCompletadas = Meta::where('usuario_id', $usuarioId)
            ->where('estado', 'completada')
            ->with('aportes')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('metas.index', compact('metasActivas', 'metasCompletadas'));
    }

    /**
     * Formulario para crear una meta nueva
     */
    public function create()
    {
        return view('metas.create');
    }

    /**
     * Mostrar meta + aportes
     */
    public function show(Meta $meta)
    {
        $this->authorizeMeta($meta);

        $meta->load(['aportes' => function($q) {
            $q->orderBy('fecha', 'desc');
        }]);

        return view('metas.show', [
            'meta'          => $meta,
            'aportes'       => $meta->aportes,
            'totalAportado' => $meta->total_aportado,
            'porcentaje'    => $meta->porcentaje,
        ]);
    }

    /**
     * Guarda una meta nueva
     */
    public function store(Request $request)
    {
        $usuarioId = auth()->id();

        // OBJETIVO
        $rawObjetivo = $request->input('objetivo', $request->input('objetivo_mask'));
        if ($rawObjetivo !== null) {
            $rawObjetivo = str_replace([',', ' '], '', $rawObjetivo);
        }

        $validated = $request->validate([
            'nombre'                  => 'required|string|max:100',
            'fecha_limite'            => 'nullable',
            'aporte_mensual_sugerido' => 'nullable|numeric|min:0',
            'estado'                  => 'nullable|in:en_curso,completada,pausada,cancelada',
        ]);

        // Validar objetivo manualmente
        if ($rawObjetivo === null || $rawObjetivo === '' || !is_numeric($rawObjetivo) || $rawObjetivo <= 0) {
            return back()
                ->withErrors(['objetivo' => 'El monto objetivo es obligatorio y debe ser mayor a 0.'])
                ->withInput();
        }

        // Convertir fecha
        $fechaLimite = $this->parseFecha($validated['fecha_limite'] ?? null);

        // Crear meta
        Meta::create([
            'usuario_id' => $usuarioId,
            'nombre'     => $validated['nombre'],
            'objetivo'   => $rawObjetivo,
            'fecha_limite' => $fechaLimite,
            'aporte_mensual_sugerido' => $validated['aporte_mensual_sugerido'] ?? null,
            'estado' => $validated['estado'] ?? 'en_curso',
        ]);

        return redirect()
            ->route('metas.index')
            ->with('success', 'Meta creada correctamente.');
    }

    /**
     * Editar
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
            'fecha_limite'  => 'nullable',
            'estado'        => 'required|in:en_curso,pausada,completada,cancelada',
            'aporte_mensual_sugerido' => 'nullable|numeric|min:0',
        ]);

        // Convertir fecha
        $data['fecha_limite'] = $this->parseFecha($data['fecha_limite'] ?? null);

        $meta->update($data);

        return redirect()
            ->route('metas.index')
            ->with('success', 'Meta actualizada correctamente.');
    }

    /**
     * Eliminar meta
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
     * Registrar aporte
     */
    public function storeAporte(Request $request, Meta $meta)
    {
        $this->authorizeMeta($meta);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required',
            'nota'  => 'nullable|string|max:255',
        ]);

        // Convertir fecha de aporte
        $fechaAporte = $this->parseFecha($data['fecha']);

        if (!$fechaAporte) {
            return back()
                ->withErrors(['fecha' => 'El formato de fecha no es válido.'])
                ->withInput();
        }

        AporteMeta::create([
            'meta_id'        => $meta->id,
            'usuario_id'     => auth()->id(),
            'transaccion_id' => null,
            'monto'          => $data['monto'],
            'fecha'          => $fechaAporte,
            'nota'           => $data['nota'] ?? null,
        ]);

        // Recalcular progreso
        $meta->refresh();

        // Si completó la meta → marcar completada
        if ($meta->porcentaje >= 100 && $meta->estado === 'en_curso') {
            $meta->estado = 'completada';
            $meta->save();
        }

        return redirect()
            ->route('metas.show', $meta)
            ->with('success', 'Aporte registrado correctamente.');
    }

    /**
     * Verifica que la meta pertenece al usuario
     */
    protected function authorizeMeta(Meta $meta)
    {
        if ($meta->usuario_id !== auth()->id()) {
            abort(403);
        }
    }
}
