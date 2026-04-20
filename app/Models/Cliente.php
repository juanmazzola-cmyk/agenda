<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'apellido', 'celular'];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    public function proximoTurno(): HasOne
    {
        return $this->hasOne(Turno::class)
            ->whereRaw("CONCAT(fecha, ' ', hora) > ?", [now()->format('Y-m-d H:i:s')])
            ->orderBy('fecha')
            ->orderBy('hora');
    }
}
