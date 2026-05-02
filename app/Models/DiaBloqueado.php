<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaBloqueado extends Model
{
    protected $table = 'dias_bloqueados';

    protected $fillable = ['fecha'];

    protected $casts = ['fecha' => 'date'];
}
