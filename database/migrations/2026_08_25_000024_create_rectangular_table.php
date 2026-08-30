<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rectangular', function (Blueprint $table) {
            $table->integer('id_rec', true);
            $table->integer('id_esp')->index('id_esp');
            $table->integer('id_cat')->index('id_cat');
            $table->decimal('precio', 10)->nullable();

            $table->foreign(['id_esp'], 'Rectangular_ibfk_1')->references(['id_esp'])->on('especialidades')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cat'], 'Rectangular_ibfk_2')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('rectangular', function (Blueprint $table) {
            $table->dropForeign('Rectangular_ibfk_1');
            $table->dropForeign('Rectangular_ibfk_2');
        });
        Schema::dropIfExists('rectangular');
    }
};
