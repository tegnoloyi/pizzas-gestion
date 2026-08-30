<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdomicilio', function (Blueprint $table) {
            $table->integer('id_pdomicilio', true);
            $table->integer('id_clie')->index('pdomicilio_clientes_fk');
            $table->integer('id_dir')->index('pdomicilio_direcciones_fk');
            $table->integer('id_venta')->index('pdomicilio_venta_fk');
            $table->integer('status')->nullable()->default(1);

            $table->foreign(['id_clie'], 'PDomicilio_Clientes_FK')->references(['id_clie'])->on('clientes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_dir'], 'PDomicilio_Direcciones_FK')->references(['id_dir'])->on('direcciones')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_venta'], 'PDomicilio_Venta_FK')->references(['id_venta'])->on('venta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pdomicilio', function (Blueprint $table) {
            $table->dropForeign('PDomicilio_Clientes_FK');
            $table->dropForeign('PDomicilio_Direcciones_FK');
            $table->dropForeign('PDomicilio_Venta_FK');
        });
        Schema::dropIfExists('pdomicilio');
    }
};
