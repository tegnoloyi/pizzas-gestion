<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hamburguesas', function (Blueprint $table) {
            $table->integer('id_hamb', true);
            $table->string('paquete')->nullable();
            $table->decimal('precio', 10);
            $table->integer('id_cat')->index('id_cat');

            $table->foreign(['id_cat'], 'Hamburguesas_ibfk_1')->references(['id_cat'])->on('categoriasprod')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('hamburguesas', function (Blueprint $table) {
            $table->dropForeign('Hamburguesas_ibfk_1');
        });
        Schema::dropIfExists('hamburguesas');
    }
};
