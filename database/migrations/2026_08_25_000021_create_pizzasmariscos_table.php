<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pizzasmariscos', function (Blueprint $table) {
            $table->integer('id_maris', true);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('id_tamañop')->index('id_tamañop');
            $table->integer('id_cat')->index('id_cat');

            $table->foreign(['id_tamañop'], 'PizzasMariscos_ibfk_1')->references(['id_tamañop'])->on('tamanospizza')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cat'], 'PizzasMariscos_ibfk_2')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pizzasmariscos', function (Blueprint $table) {
            $table->dropForeign('PizzasMariscos_ibfk_1');
            $table->dropForeign('PizzasMariscos_ibfk_2');
        });
        Schema::dropIfExists('pizzasmariscos');
    }
};
