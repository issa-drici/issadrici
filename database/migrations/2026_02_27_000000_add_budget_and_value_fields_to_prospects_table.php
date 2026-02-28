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
        Schema::table('prospects', function (Blueprint $table) {
            // Budget et valeur
            $table->decimal('budget_estime', 12, 2)->nullable()->after('niveau_interet');
            $table->text('douleur')->nullable()->after('budget_estime');
            $table->decimal('valeur_perdue_actuelle', 12, 2)->nullable()->after('douleur');
            $table->decimal('valeur_deal', 12, 2)->nullable()->after('valeur_perdue_actuelle');
            
            // Résultat du deal
            $table->decimal('montant_gagne', 12, 2)->nullable()->after('valeur_deal');
            $table->decimal('montant_perdu', 12, 2)->nullable()->after('montant_gagne');
            
            // Proposition envoyée
            $table->decimal('montant_proposition', 12, 2)->nullable()->after('montant_perdu');
            $table->string('duree_proposition')->nullable()->after('montant_proposition'); // ex: "12 mois", "6 mois", etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn([
                'budget_estime',
                'douleur',
                'valeur_perdue_actuelle',
                'valeur_deal',
                'montant_gagne',
                'montant_perdu',
                'montant_proposition',
                'duree_proposition',
            ]);
        });
    }
};
