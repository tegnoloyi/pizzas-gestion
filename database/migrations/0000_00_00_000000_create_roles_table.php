<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Si es true, el usuario con este rol debe tener una caja abierta
            // para poder operar (acceder al POS, registrar salidas de stock, etc.).
            // Si es false, puede trabajar sin caja (ej. operador de inventario puro).
            $table->boolean('requiere_caja')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
