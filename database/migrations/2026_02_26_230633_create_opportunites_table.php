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
        Schema::create('opportunites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            
            // Stade de l'opportunité
            $table->string('stade')->default('decouverte'); // decouverte, proposition, negociation, gagne, perdu
            
            // Estimation
            $table->decimal('montant_estime', 12, 2)->nullable();
            $table->integer('probabilite')->default(0); // 0-100
            $table->date('date_estimee_decision')->nullable();
            
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
        Schema::dropIfExists('opportunites');
    }
};
