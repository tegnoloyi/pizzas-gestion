<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Los IDs son fijos a propósito: el JS del POS (public/js/pos.js,
     * método procesarOrdenFinal) manda id_metpago = 1 (Tarjeta),
     * 2 (Efectivo) y 3 (Transferencia) por código duro. Si alguna vez
     * cambias este orden, hay que actualizar también el JS.
     */
    public function run(): void
    {
        DB::table('metodospago')->updateOrInsert(['id_metpago' => 1], ['metodo' => 'Tarjeta']);
        DB::table('metodospago')->updateOrInsert(['id_metpago' => 2], ['metodo' => 'Efectivo']);
        DB::table('metodospago')->updateOrInsert(['id_metpago' => 3], ['metodo' => 'Transferencia']);
    }
}
