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
        Schema::create('historial_inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->nullable()->constrained()->onDelete('set null'); // nullable to keep history if product is hard deleted (though we use soft deletes)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who performed the action
            $table->string('accion'); // 'CREADO', 'EDITADO', 'ELIMINADO', 'RESTAURADO'
            $table->json('detalles')->nullable(); // JSON object with details of the changes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_inventarios');
    }
};
