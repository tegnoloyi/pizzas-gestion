<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->integer('id_venta', true);
            $table->integer('id_suc')->index('id_suc');
            $table->integer('mesa')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->decimal('total', 10)->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->string('comentarios')->nullable();
            $table->integer('tipo_servicio')->nullable();
            $table->string('nombreClie', 100)->nullable();
            $table->integer('id_caja')->index('venta_caja_fk');
            $table->string('detalles', 100)->nullable();

            $table->foreign(['id_caja'], 'Venta_Caja_FK')->references(['id_caja'])->on('caja')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_suc'], 'Venta_ibfk_1')->references(['id_suc'])->on('sucursal')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign('Venta_Caja_FK');
            $table->dropForeign('Venta_ibfk_1');
        });
        Schema::dropIfExists('venta');
    }
};
