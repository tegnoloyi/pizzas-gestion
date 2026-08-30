<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->integer('id_emp', true);
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->integer('id_ca')->index('id_ca');
            $table->integer('id_suc')->index('id_suc');
            $table->string('nickName', 50)->nullable()->unique('userunique');
            $table->string('password')->nullable();
            $table->boolean('status');

            $table->foreign(['id_ca'], 'Empleados_ibfk_1')->references(['id_ca'])->on('cargos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_suc'], 'Empleados_ibfk_2')->references(['id_suc'])->on('sucursal')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign('Empleados_ibfk_1');
            $table->dropForeign('Empleados_ibfk_2');
        });
        Schema::dropIfExists('empleados');
    }
};
