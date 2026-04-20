<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turno extends Model
{
    protected $fillable = [
        'cliente_id',
        'tratamiento_id',
        'fecha',
        'hora',
        'valor',
        'cobrado',
        'notas',
    ];

    protected $casts = [
        'fecha'   => 'date',
        'cobrado' => 'boolean',
        'valor'   => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tratamiento(): BelongsTo
    {
        return $this->belongsTo(Tratamiento::class);
    }
}
