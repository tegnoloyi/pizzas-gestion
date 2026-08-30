<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->integer('id_gastos', true);
            $table->integer('id_suc')->index('id_suc');
            $table->string('descripcion');
            $table->decimal('precio', 10);
            $table->dateTime('fecha')->nullable()->useCurrent();
            $table->boolean('evaluado')->nullable();
            $table->integer('id_caja')->index('gastos_caja_fk');
            $table->integer('id_emp')->index('gastos_empleados_fk');

            $table->foreign(['id_caja'], 'Gastos_Caja_FK')->references(['id_caja'])->on('caja')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_suc'], 'Gastos_ibfk_1')->references(['id_suc'])->on('sucursal')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_emp'], 'Gastos_Empleados_FK')->references(['id_emp'])->on('empleados')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign('Gastos_Caja_FK');
            $table->dropForeign('Gastos_ibfk_1');
            $table->dropForeign('Gastos_Empleados_FK');
        });
        Schema::dropIfExists('gastos');
    }
};