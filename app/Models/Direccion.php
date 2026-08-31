<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'id_dir';
    public $timestamps = false;

    protected $fillable = ['id_clie', 'calle', 'manzana', 'lote', 'colonia', 'referencia', 'status'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_clie', 'id_clie');
    }
}
