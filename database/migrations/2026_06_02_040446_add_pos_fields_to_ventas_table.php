<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('descuento_monto', 10, 2)->default(0)->after('total');
            $table->boolean('requiere_factura')->default(false)->after('descuento_monto');
            $table->decimal('pago_con', 10, 2)->nullable()->after('requiere_factura');
            $table->decimal('cambio', 10, 2)->nullable()->after('pago_con');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            //
        });
    }
};
