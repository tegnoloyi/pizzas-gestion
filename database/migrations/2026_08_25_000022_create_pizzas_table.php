<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pizzas', function (Blueprint $table) {
            $table->integer('id_pizza', true);
            $table->integer('id_esp')->index('pizzas_especialidades_fk');
            $table->integer('id_tamano')->index('pizzas_tamanospizza_fk');
            $table->integer('id_cat')->index('pizzas_categoriasprod_fk');

            $table->foreign(['id_cat'], 'Pizzas_CategoriasProd_FK')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_esp'], 'Pizzas_Especialidades_FK')->references(['id_esp'])->on('especialidades')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tamano'], 'Pizzas_TamanosPizza_FK')->references(['id_tamañop'])->on('tamanospizza')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pizzas', function (Blueprint $table) {
            $table->dropForeign('Pizzas_CategoriasProd_FK');
            $table->dropForeign('Pizzas_Especialidades_FK');
            $table->dropForeign('Pizzas_TamanosPizza_FK');
        });
        Schema::dropIfExists('pizzas');
    }
};
