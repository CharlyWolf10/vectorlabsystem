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
        Schema::table('productos', function (Blueprint $table) {
            $table->string('ingreso_tipo_default')->default('unidad')->after('categoria')->nullable();
            $table->integer('ingreso_paquetes_default')->default(1)->after('ingreso_tipo_default')->nullable();
            $table->integer('ingreso_unidades_default')->default(1)->after('ingreso_paquetes_default')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['ingreso_tipo_default', 'ingreso_paquetes_default', 'ingreso_unidades_default']);
        });
    }
};
