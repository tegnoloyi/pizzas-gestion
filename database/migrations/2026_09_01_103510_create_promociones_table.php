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
            // Días en que la promo se activa sola (JSON, ej. [4] = solo jueves). NULL/[] = solo switch manual.
            $table->text('dias_semana')->nullable();
            $table->boolean('activa')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};