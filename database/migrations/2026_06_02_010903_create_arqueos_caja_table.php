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
        Schema::create('arqueos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('fondo_inicial', 10, 2)->default(0);
            $table->integer('monedas_50c')->default(0);
            $table->integer('monedas_1')->default(0);
            $table->integer('monedas_2')->default(0);
            $table->integer('monedas_5')->default(0);
            $table->integer('monedas_10')->default(0);
            $table->integer('monedas_20')->default(0);
            $table->integer('billetes_50')->default(0);
            $table->integer('billetes_100')->default(0);
            $table->integer('billetes_200')->default(0);
            $table->integer('billetes_500')->default(0);
            $table->decimal('total_calculado', 12, 2)->default(0);
            $table->decimal('total_registrado_sistema', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueos_caja');
    }
};
