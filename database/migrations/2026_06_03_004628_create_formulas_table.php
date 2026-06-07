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
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio_costo', 10, 2)->default(0);
            $table->decimal('margen_ganancia', 5, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->integer('minutos_produccion')->default(0);
            $table->decimal('costo_operativo', 10, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
