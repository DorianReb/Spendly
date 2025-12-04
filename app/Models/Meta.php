<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AporteMeta;

class Meta extends Model
{
    use SoftDeletes;

    protected $table = 'metas';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'objetivo',                 // 👈 campo correcto en BD
        'fecha_limite',
        'aporte_mensual_sugerido',
        'estado',
    ];

    // =========================
    // RELACIONES
    // =========================

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function aportes()
    {
        return $this->hasMany(AporteMeta::class, 'meta_id');
    }

    // =========================
    // ACCESSORS (cálculos)
    // =========================

    /**
     * Total aportado a la meta (suma de todos los aportes).
     * Se puede usar como: $meta->total_aportado
     */
    public function getTotalAportadoAttribute()
    {
        // suma todo lo aportado (puedes usar relación o query directa)
        return $this->aportes()->sum('monto');
    }

    /**
     * Monto restante para llegar al objetivo.
     * Se puede usar como: $meta->restante
     */
    public function getRestanteAttribute()
    {
        $objetivo = $this->objetivo ?? 0;

        return max(0, $objetivo - $this->total_aportado);
    }

    /**
     * Porcentaje de avance de la meta.
     * Se puede usar como: $meta->porcentaje
     */
    public function getPorcentajeAttribute()
    {
        $objetivo = $this->objetivo ?? 0;

        if ($objetivo <= 0) {
            return 0;
        }

        return (int) round(($this->total_aportado * 100) / $objetivo);
    }
}
