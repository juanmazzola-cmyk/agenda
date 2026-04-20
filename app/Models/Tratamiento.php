<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tratamiento extends Model
{
    protected $fillable = ['nombre'];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }
}
