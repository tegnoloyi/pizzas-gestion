<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La migración original creó la columna 'activo', pero el modelo Promocion,
 * el PromocionController y la vista promos/index.blade.php usan 'activa'
 * en todo el código. Esta migración renombra la columna para que coincida
 * y no truene con "Unknown column 'activa'" al crear/activar una promoción.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('promociones', 'activo') && !Schema::hasColumn('promociones', 'activa')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->renameColumn('activo', 'activa');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('promociones', 'activa') && !Schema::hasColumn('promociones', 'activo')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->renameColumn('activa', 'activo');
            });
        }
    }
};
