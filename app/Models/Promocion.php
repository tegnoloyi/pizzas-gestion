<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'clave',
        'activa',
        'dias_semana',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'dias_semana' => 'array',
    ];

    /**
     * True si la promo debe considerarse activa HOY, ya sea porque:
     * a) el switch manual está en "Activa" (fuerza encendida cualquier día), o
     * b) hoy es uno de los días de la semana que tiene programados (dias_semana).
     */
    public function estaActivaHoy(): bool
    {
        if ($this->activa) {
            return true;
        }

        $dias = $this->dias_semana ?? [];
        $hoy = now()->dayOfWeek; // Carbon: domingo=0 ... sábado=6

        return in_array($hoy, $dias);
    }
}