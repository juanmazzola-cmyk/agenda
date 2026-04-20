<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table    = 'configuracion';
    protected $fillable = ['clave', 'valor'];

    public static function obtener(string $clave, string $default = ''): string
    {
        return static::where('clave', $clave)->value('valor') ?? $default;
    }

    public static function establecer(string $clave, string $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
