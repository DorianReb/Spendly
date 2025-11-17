<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'descripcion',
        'icon',
        'tipo',
        'color_hex',
        'orden',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden'  => 'integer',
    ];

    /* ==========
     * Relaciones
     * ========== */

    public function usuario()
    {
        // Asumiendo modelo App\Models\Usuario
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'categoria_id');
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class, 'categoria_id');
    }

    public function recurrencias()
    {
        return $this->hasMany(Recurrencia::class, 'categoria_id');
    }

    /* ==========
     *  Scopes
     * ========== */

    public function scopeForUser($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeGastos($query)
    {
        return $query->where('tipo', 'gasto');
    }

    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
