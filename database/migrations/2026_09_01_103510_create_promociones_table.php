<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->integer('id_promo', true);
            $table->string('clave', 50)->unique(); // identificador fijo que usa el código, ej: '2x1_tamano'
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};