<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal', function (Blueprint $table) {
            $table->integer('id_suc', true);
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal');
    }
};
