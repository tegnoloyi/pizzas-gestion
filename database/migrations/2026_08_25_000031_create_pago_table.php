<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago', function (Blueprint $table) {
            $table->integer('id_pago', true);
            $table->integer('id_venta')->index('id_venta');
            $table->integer('id_metpago')->index('id_metpago');
            $table->decimal('monto', 10);
            $table->string('referencia', 100)->nullable();

            $table->foreign(['id_venta'], 'Pago_ibfk_1')->references(['id_venta'])->on('venta')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_metpago'], 'Pago_ibfk_2')->references(['id_metpago'])->on('metodospago')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropForeign('Pago_ibfk_1');
            $table->dropForeign('Pago_ibfk_2');
        });
        Schema::dropIfExists('pago');
    }
};
