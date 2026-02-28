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
        Schema::create('preparation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            
            // Préparation du message
            $table->string('angle_choisi')->nullable();
            $table->text('observation_utilisee')->nullable();
            $table->text('question_prevue')->nullable();
            $table->text('objection_probable')->nullable();
            
            // Statut
            $table->string('statut')->default('en_preparation'); // en_preparation, utilise, archive
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preparation_messages');
    }
};
