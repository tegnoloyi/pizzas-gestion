<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alitas', function (Blueprint $table) {
            $table->integer('id_alis', true);
            $table->string('orden')->nullable();
            $table->decimal('precio', 10);
            $table->integer('id_cat')->index('id_cat');

            $table->foreign(['id_cat'], 'Alitas_ibfk_1')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('alitas', function (Blueprint $table) {
            $table->dropForeign('Alitas_ibfk_1');
        });
        Schema::dropIfExists('alitas');
    }
};
