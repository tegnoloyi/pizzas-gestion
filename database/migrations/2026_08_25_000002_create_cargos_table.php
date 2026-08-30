<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->integer('id_ca', true);
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
