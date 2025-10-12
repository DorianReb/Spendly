<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'email',
        'password_hash',
        'es_premium',
        'fecha_premium',
        'remember_token', // si existe la columna (opcional)
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * MUY IMPORTANTE:
     * Dile a Laravel qué columna usar como "password" para auth.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;   // 👈 mapea a tu columna real
    }

    // Quita casts que no existan en tu esquema:
    // - Si NO tienes email_verified_at en 'usuarios', no lo declares.
    // - El cast 'hashed' no hace falta; ya guardas con Hash::make en el registro.
}
