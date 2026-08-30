<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoriasprod', function (Blueprint $table) {
            $table->integer('id_cat', true);
            $table->string('descripcion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoriasprod');
    }
};
