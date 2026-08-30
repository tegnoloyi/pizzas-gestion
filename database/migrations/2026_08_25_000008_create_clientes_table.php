<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->integer('id_clie', true);
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->integer('status')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
