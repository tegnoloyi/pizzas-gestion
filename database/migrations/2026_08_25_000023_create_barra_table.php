<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barra', function (Blueprint $table) {
            $table->integer('id_barr', true);
            $table->integer('id_especialidad')->index('id_especialidad');
            $table->integer('id_cat')->index('id_cat');
            $table->decimal('precio', 10);

            $table->foreign(['id_especialidad'], 'Barra_ibfk_1')->references(['id_esp'])->on('especialidades')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cat'], 'Barra_ibfk_2')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('barra', function (Blueprint $table) {
            $table->dropForeign('Barra_ibfk_1');
            $table->dropForeign('Barra_ibfk_2');
        });
        Schema::dropIfExists('barra');
    }
};
