<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use App\Models\Categoria;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 2ª pantalla: lista por categoría
    public function porCategoria(Categoria $categoria, Request $request)
    {
        $usuarioId = auth()->user()->id; // o auth()->id()
        $tipo = $request->query('tipo', 'gasto'); // gasto | ingreso

        $transacciones = Transaccion::where('usuario_id', $usuarioId)
            ->where('categoria_id', $categoria->id)
            ->where('tipo', $tipo)
            ->orderByDesc('fecha')
            ->get();

        $total = $transacciones->sum('importe');

        return view('transacciones.por_categoria', [
            'categoria'      => $categoria,
            'transacciones'  => $transacciones,
            'total'          => $total,
            'tipo'           => $tipo,
        ]);
    }

    // 3ª pantalla: detalle
    public function show(Transaccion $transaccion)
    {
        $this->authorizeTransaccion($transaccion);

        return view('transacciones.show', [
            'transaccion' => $transaccion,
        ]);
    }

    public function create(Request $request)
    {
        $tipo = $request->query('tipo', 'gasto');

        return view('transacciones.create', compact('tipo'));
    }

    public function store(Request $request)
    {
        $usuarioId = auth()->id();

        $validated = $request->validate([
            'tipo'        => 'required|in:ingreso,gasto',
            'categoria_id'=> 'required|exists:categorias,id',
            'importe'     => 'required|numeric|min:0.01',
            'fecha'       => 'required|date|before_or_equal:today',
            'nota'        => 'nullable|string|max:255',
        ]);

        $validated['usuario_id'] = auth()->id();

        Transaccion::create($validated);

        return redirect()
            ->route('home')
            ->with('success', 'Transacción registrada.');
    }

    public function edit(Transaccion $transaccion)
    {
        $this->authorizeTransaccion($transaccion);

        $usuarioId = auth()->user()->id;
        $tipo = $transaccion->tipo; // gasto | ingreso

        $categorias = Categoria::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->where('activa', 1)
            ->orderBy('orden')
            ->get();

        return view('transacciones.edit', [
            'transaccion' => $transaccion,
            'categorias'  => $categorias,
            'tipo'        => $tipo,
        ]);
    }

    public function update(Request $request, Transaccion $transaccion)
    {
        $this->authorizeTransaccion($transaccion);

        $validated = $request->validate([
            'categoria_id'=> 'required|exists:categorias,id',
            'importe'     => 'required|numeric|min:0.01',
            'fecha'       => 'required|date|before_or_equal:today',
            'nota'        => 'nullable|string|max:255',
        ]);

        $transaccion->update($validated);

        return redirect()
            ->route('transacciones.show', $transaccion)
            ->with('success','Transacción actualizada.');
    }

    public function destroy(Transaccion $transaccion)
    {
        $this->authorizeTransaccion($transaccion);

        $transaccion->delete();

        return redirect()
            ->route('home')
            ->with('success','Transacción eliminada.');
    }

    protected function authorizeTransaccion(Transaccion $transaccion)
    {
        /*dd([
            'auth_id'        => auth()->id(),
            'trx_usuario_id' => $transaccion->usuario_id,
            'transaccion_id' => $transaccion->id,
        ]);
        */

        if ($transaccion->usuario_id !== auth()->id()) {
            abort(403);
        }
    }


}

