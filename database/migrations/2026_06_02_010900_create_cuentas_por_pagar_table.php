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
        Schema::create('cuentas_por_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('compra_id')->nullable()->constrained('compras');
            $table->decimal('monto_total', 12, 2);
            $table->decimal('saldo_pendiente', 12, 2);
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_pagar');
    }
};
