<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pversion', function (Blueprint $table) {
            $table->integer('id_pversion', true);
            $table->integer('id_suc')->index('pversion_sucursal_fk');
            $table->bigInteger('version')->default(0);

            $table->foreign(['id_suc'], 'PVersion_Sucursal_FK')->references(['id_suc'])->on('sucursal')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('pversion', function (Blueprint $table) {
            $table->dropForeign('PVersion_Sucursal_FK');
        });
        Schema::dropIfExists('pversion');
    }
};
