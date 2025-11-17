<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LISTADO
    public function index()
    {
        $usuarioId = auth()->id();

        $categoriasGasto = Categoria::forUser($usuarioId)
            ->gastos()->activas()->ordenadas()->get();

        $categoriasIngreso = Categoria::forUser($usuarioId)
            ->ingresos()->activas()->ordenadas()->get();

        // ahora plural
        return view('categorias.index', compact('categoriasGasto', 'categoriasIngreso'));
    }

    // CREATE
    public function create(Request $request)
    {
        $tipo = $request->query('tipo', 'gasto');
        abort_unless(in_array($tipo, ['gasto', 'ingreso']), 404);

        return view('categorias.create', compact('tipo'));
    }

    // STORE
    // STORE
    public function store(Request $request)
    {
        $usuarioId = auth()->id();

        $request->merge([
            'tipo' => $request->input('tipo', 'gasto'),
        ]);

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categorias')->where(function ($q) use ($usuarioId, $request) {
                    return $q->where('usuario_id', $usuarioId)
                        ->where('tipo', $request->tipo);
                }),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:60'],   // 👈 nuevo
            'tipo'        => ['required', Rule::in(['ingreso', 'gasto'])],
            'color_hex'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'nombre.unique' => 'Ya tienes una categoría con ese nombre y tipo.',
        ]);

        $maxOrden = Categoria::forUser($usuarioId)->max('orden') ?? 0;

        Categoria::create([
            'usuario_id'  => $usuarioId,
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'icon'        => $request->icon ?: 'fa-circle-question', // 👈 default
            'tipo'        => $request->tipo,
            'color_hex'   => $request->color_hex ?: '#6C63FF',
            'orden'       => $maxOrden + 1,
            'activa'      => true,
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }


    // EDIT
    public function edit(Categoria $categoria)
    {
        $this->authorizeCategoria($categoria);

        return view('categorias.edit', compact('categoria'));
    }

    // UPDATE
    public function update(Request $request, Categoria $categoria)
    {
        $this->authorizeCategoria($categoria);

        $usuarioId = auth()->id();

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categorias')->where(function ($q) use ($usuarioId, $request) {
                    return $q->where('usuario_id', $usuarioId)
                        ->where('tipo', $request->tipo);
                })->ignore($categoria->id),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:60'],   // 👈
            'tipo'        => ['required', Rule::in(['ingreso', 'gasto'])],
            'color_hex'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'activa'      => ['nullable', 'boolean'],
        ]);

        $categoria->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'icon'        => $request->icon ?: $categoria->icon,
            'tipo'        => $request->tipo,
            'color_hex'   => $request->color_hex ?: $categoria->color_hex,
            'activa'      => $request->has('activa') ? (bool)$request->activa : $categoria->activa,
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }


    // DESTROY
    public function destroy(Categoria $categoria)
    {
        $this->authorizeCategoria($categoria);

        $categoria->delete();

        return redirect()
            ->route('categorias.index')   // PLURAL
            ->with('success', 'Categoría eliminada.');
    }

    public function reorder(Request $request)
    {
        $usuarioId = auth()->id();

        $request->validate([
            'orden'   => ['required', 'array'],
            'orden.*' => ['integer', 'exists:categorias,id'],
        ]);

        $pos = 1;
        foreach ($request->orden as $catId) {
            Categoria::forUser($usuarioId)
                ->where('id', $catId)
                ->update(['orden' => $pos++]);
        }

        return response()->json(['ok' => true]);
    }

    protected function authorizeCategoria(Categoria $categoria)
    {
        if ($categoria->usuario_id !== auth()->id()) {
            abort(403);
        }
    }
}
