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
        Schema::create('gasto_operativos', function (Blueprint $table) {
            $table->id();
            $table->decimal('renta', 10, 2)->default(0);
            $table->decimal('luz', 10, 2)->default(0);
            $table->decimal('agua', 10, 2)->default(0);
            $table->decimal('sueldos', 10, 2)->default(0);
            $table->decimal('otros_gastos', 10, 2)->default(0);
            $table->integer('dias_por_mes')->default(24);
            $table->integer('horas_por_dia')->default(8);
            $table->decimal('costo_por_minuto', 10, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_operativos');
    }
};
