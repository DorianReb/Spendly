<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AporteMeta extends Model
{
    protected $table = 'aportes_meta';

    protected $fillable = [
        'meta_id',
        'usuario_id',
        'monto',
        'fecha',
        'nota',
    ];

    public function meta()
    {
        return $this->belongsTo(Meta::class, 'meta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
