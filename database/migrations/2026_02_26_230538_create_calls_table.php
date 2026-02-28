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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            
            // Planification
            $table->dateTime('date_planifiee');
            $table->text('objectif_call');
            $table->text('points_a_verifier')->nullable();
            
            // Résultat
            $table->dateTime('date_realisee')->nullable();
            $table->text('resultat')->nullable();
            $table->string('statut')->default('planifie'); // planifie, realise, annule
            $table->text('prochaine_etape')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
