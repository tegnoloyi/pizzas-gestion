<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->integer('id_permiso', true);
            $table->integer('id_cargo')->unique('id_cargo');
            $table->boolean('crear_producto');
            $table->boolean('modificar_producto');
            $table->boolean('eliminar_producto');
            $table->boolean('ver_producto');
            $table->boolean('crear_empleado');
            $table->boolean('modificar_empleado');
            $table->boolean('eliminar_empleado');
            $table->boolean('ver_empleado');
            $table->boolean('crear_venta');
            $table->boolean('modificar_venta');
            $table->boolean('eliminar_venta');
            $table->boolean('ver_venta');
            $table->boolean('crear_recurso');
            $table->boolean('modificar_recurso');
            $table->boolean('eliminar_recurso');
            $table->boolean('ver_recurso');

            $table->foreign(['id_cargo'], 'Permisos_Cargos_FK')->references(['id_ca'])->on('cargos')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->dropForeign('Permisos_Cargos_FK');
        });
        Schema::dropIfExists('permisos');
    }
};
