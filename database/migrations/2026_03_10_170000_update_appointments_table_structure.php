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
        Schema::table('appointments', function (Blueprint $table) {
            // Eliminar campos actuales que no coinciden con las especificaciones
            $table->dropColumn('appointment_time');
            $table->dropColumn('status');
            
            // Agregar campos según especificaciones
            $table->time('start_time')->after('appointment_date');
            $table->time('end_time')->after('start_time');
            $table->integer('duration')->default(15)->after('end_time');
            $table->tinyInteger('status')->default(1)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Revertir cambios
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('duration');
            $table->dropColumn('status');
            
            // Restaurar campos originales
            $table->time('appointment_time')->after('appointment_date');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'rescheduled'])->default('scheduled')->after('notes');
        });
    }
};
