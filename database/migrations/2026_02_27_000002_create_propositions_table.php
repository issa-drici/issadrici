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
        Schema::create('propositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            
            // Détails de la proposition
            $table->decimal('montant', 12, 2)->nullable();
            $table->string('duree')->nullable(); // Ex: "12 mois", "6 mois", "1 an"
            $table->date('date_envoi')->nullable();
            $table->string('statut')->default('envoyee'); // envoyee, acceptee, refusee, en_negociation
            
            // Détails
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propositions');
    }
};
