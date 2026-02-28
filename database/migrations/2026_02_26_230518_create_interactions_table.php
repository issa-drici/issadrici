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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            
            // Type d'interaction
            $table->string('type'); // linkedin_invitation, linkedin_message, email, relance, proposition_envoyee
            
            // Détails
            $table->dateTime('date');
            $table->string('statut')->default('prevu'); // prevu, envoye, repondu, termine
            $table->string('resume')->nullable(); // courte phrase
            $table->string('resultat')->nullable(); // positif, neutre, negatif
            
            // Contenu (optionnel, pour contexte)
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
