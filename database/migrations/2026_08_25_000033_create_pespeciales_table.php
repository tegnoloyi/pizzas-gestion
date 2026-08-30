<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pespeciales', function (Blueprint $table) {
            $table->integer('id_pespeciales', true);
            $table->integer('id_venta')->index('pespeciales_venta_fk');
            $table->integer('id_dir')->nullable()->index('pespeciales_direcciones_fk');
            $table->integer('id_clie')->nullable()->index('pespeciales_clientes_fk');
            $table->decimal('anticipo', 10)->nullable()->default(0);
            $table->dateTime('fecha_creacion')->nullable()->useCurrent();
            $table->dateTime('fecha_entrega')->nullable();
            $table->integer('status')->default(1);

            $table->foreign(['id_clie'], 'PEspeciales_Clientes_FK')->references(['id_clie'])->on('clientes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_dir'], 'PEspeciales_Direcciones_FK')->references(['id_dir'])->on('direcciones')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_venta'], 'PEspeciales_Venta_FK')->references(['id_venta'])->on('venta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pespeciales', function (Blueprint $table) {
            $table->dropForeign('PEspeciales_Clientes_FK');
            $table->dropForeign('PEspeciales_Direcciones_FK');
            $table->dropForeign('PEspeciales_Venta_FK');
        });
        Schema::dropIfExists('pespeciales');
    }
};
