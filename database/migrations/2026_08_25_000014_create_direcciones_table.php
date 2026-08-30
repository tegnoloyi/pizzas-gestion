<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->integer('id_dir', true);
            $table->integer('id_clie')->index('direcciones_clientes_fk');
            $table->string('calle', 100);
            $table->string('manzana', 100)->nullable();
            $table->string('lote', 100)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('referencia', 1000)->nullable();
            $table->integer('status')->default(1);

            $table->foreign(['id_clie'], 'Direcciones_Clientes_FK')->references(['id_clie'])->on('clientes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropForeign('Direcciones_Clientes_FK');
        });
        Schema::dropIfExists('direcciones');
    }
};
