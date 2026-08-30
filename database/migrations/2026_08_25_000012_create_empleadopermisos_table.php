<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleadopermisos', function (Blueprint $table) {
            $table->bigIncrements('id_permiso_emp');
            $table->integer('id_emp')->index('empleadopermisos_id_emp_foreign');
            $table->string('modulo', 50);
            $table->boolean('mostrar')->default(false);
            $table->boolean('crear')->default(false);
            $table->boolean('editar')->default(false);
            $table->boolean('eliminar')->default(false);
            $table->boolean('gestionar')->default(false);
            $table->timestamps();

            $table->unique(['id_emp', 'modulo']);

            $table->foreign(['id_emp'])->references(['id_emp'])->on('empleados')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('empleadopermisos', function (Blueprint $table) {
            $table->dropForeign('empleadopermisos_id_emp_foreign');
        });
        Schema::dropIfExists('empleadopermisos');
    }
};
