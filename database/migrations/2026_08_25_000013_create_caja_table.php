<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->integer('id_caja', true);
            $table->integer('id_suc')->index('caja_sucursal_fk');
            $table->integer('id_emp')->index('caja_empleados_fk');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 10, 0);
            $table->decimal('monto_final', 10, 0)->nullable();
            $table->integer('status')->default(1);
            $table->string('observaciones_apertura')->nullable();
            $table->string('observaciones_cierre')->nullable();

            $table->foreign(['id_emp'], 'Caja_Empleados_FK')->references(['id_emp'])->on('empleados')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_suc'], 'Caja_Sucursal_FK')->references(['id_suc'])->on('sucursal')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('caja', function (Blueprint $table) {
            $table->dropForeign('Caja_Empleados_FK');
            $table->dropForeign('Caja_Sucursal_FK');
        });
        Schema::dropIfExists('caja');
    }
};
