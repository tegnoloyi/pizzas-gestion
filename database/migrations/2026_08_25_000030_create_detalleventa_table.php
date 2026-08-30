<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalleventa', function (Blueprint $table) {
            $table->integer('id_detalle', true);
            $table->integer('id_venta')->index('id_venta');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10)->nullable();
            $table->integer('id_hamb')->nullable()->index('id_hamb');
            $table->integer('id_cos')->nullable()->index('id_cos');
            $table->integer('id_alis')->nullable()->index('id_alis');
            $table->integer('id_spag')->nullable()->index('id_spag');
            $table->integer('id_papa')->nullable()->index('id_papa');
            $table->longText('id_rec')->nullable()->index('id_rec');
            $table->longText('id_barr')->nullable()->index('id_barr');
            $table->integer('id_maris')->nullable()->index('id_maris');
            $table->integer('id_refresco')->nullable()->index('id_refresco');
            $table->longText('id_paquete')->nullable()->index('id_paquete1');
            $table->longText('id_magno')->nullable()->index('id_magno');
            $table->integer('id_pizza')->nullable()->index('detalleventa_pizzas_fk');
            $table->integer('status')->nullable()->default(1);
            $table->longText('ingredientes')->nullable();
            $table->integer('queso')->nullable();
            $table->longText('pizza_mitad')->nullable();

            $table->foreign(['id_venta'], 'DetalleVenta_ibfk_1')->references(['id_venta'])->on('venta')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_refresco'], 'DetalleVenta_ibfk_10')->references(['id_refresco'])->on('refrescos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_hamb'], 'DetalleVenta_ibfk_2')->references(['id_hamb'])->on('hamburguesas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cos'], 'DetalleVenta_ibfk_3')->references(['id_cos'])->on('costillas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_alis'], 'DetalleVenta_ibfk_4')->references(['id_alis'])->on('alitas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_spag'], 'DetalleVenta_ibfk_5')->references(['id_spag'])->on('spaguetty')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_papa'], 'DetalleVenta_ibfk_6')->references(['id_papa'])->on('ordendepapas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_maris'], 'DetalleVenta_ibfk_9')->references(['id_maris'])->on('pizzasmariscos')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_pizza'], 'DetalleVenta_Pizzas_FK')->references(['id_pizza'])->on('pizzas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('detalleventa', function (Blueprint $table) {
            $table->dropForeign('DetalleVenta_ibfk_1');
            $table->dropForeign('DetalleVenta_ibfk_10');
            $table->dropForeign('DetalleVenta_ibfk_2');
            $table->dropForeign('DetalleVenta_ibfk_3');
            $table->dropForeign('DetalleVenta_ibfk_4');
            $table->dropForeign('DetalleVenta_ibfk_5');
            $table->dropForeign('DetalleVenta_ibfk_6');
            $table->dropForeign('DetalleVenta_ibfk_9');
            $table->dropForeign('DetalleVenta_Pizzas_FK');
        });
        Schema::dropIfExists('detalleventa');
    }
};
