<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alita extends Model
{
    protected $table = 'alitas';
    protected $primaryKey = 'id_alis';
    public $timestamps = false;

    protected $fillable = ['orden', 'precio', 'id_cat'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_cat', 'id_cat');
    }
}
