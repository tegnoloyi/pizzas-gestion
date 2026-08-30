<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamanospizza', function (Blueprint $table) {
            $table->integer('id_tamañop', true);
            $table->string('tamano', 50);
            $table->decimal('precio', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamanospizza');
    }
};
