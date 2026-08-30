<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magno', function (Blueprint $table) {
            $table->integer('id_magno', true);
            $table->integer('id_especialidad')->index('id_especialidad');
            $table->integer('id_refresco')->index('id_refresco');
            $table->decimal('precio', 10);

            $table->foreign(['id_especialidad'], 'Magno_ibfk_1')->references(['id_esp'])->on('especialidades')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_refresco'], 'Magno_ibfk_2')->references(['id_refresco'])->on('refrescos')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('magno', function (Blueprint $table) {
            $table->dropForeign('Magno_ibfk_1');
            $table->dropForeign('Magno_ibfk_2');
        });
        Schema::dropIfExists('magno');
    }
};
