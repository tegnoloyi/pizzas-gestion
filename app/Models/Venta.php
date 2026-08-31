<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_suc', 'mesa', 'fecha_hora', 'total', 'status',
        'comentarios', 'tipo_servicio', 'nombreClie', 'id_caja', 'detalles',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_suc', 'id_suc');
    }

    public function pespecial()
    {
        return $this->hasOne(PEspecial::class, 'id_venta', 'id_venta');
    }
}
