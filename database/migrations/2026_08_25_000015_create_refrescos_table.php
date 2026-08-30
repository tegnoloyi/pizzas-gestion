<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refrescos', function (Blueprint $table) {
            $table->integer('id_refresco', true);
            $table->string('nombre');
            $table->integer('id_tamano')->index('id_tamano');
            $table->integer('id_cat')->index('id_cat');

            $table->foreign(['id_tamano'], 'Refrescos_ibfk_1')->references(['id_tamano'])->on('tamanosrefrescos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cat'], 'Refrescos_ibfk_2')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('refrescos', function (Blueprint $table) {
            $table->dropForeign('Refrescos_ibfk_1');
            $table->dropForeign('Refrescos_ibfk_2');
        });
        Schema::dropIfExists('refrescos');
    }
};
