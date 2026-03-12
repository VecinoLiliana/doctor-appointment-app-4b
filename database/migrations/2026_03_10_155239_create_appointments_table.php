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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->onDelete('cascade');
                
            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->onDelete('cascade');
                
            $table->foreignId('speciality_id')
                ->nullable()
                ->constrained('specialities')
                ->onDelete('set null');
            
            // Campos de la cita
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'rescheduled'])
                  ->default('scheduled');
            $table->text('notes')->nullable();
            $table->text('reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
