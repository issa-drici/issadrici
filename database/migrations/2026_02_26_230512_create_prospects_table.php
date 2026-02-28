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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            
            // Identité
            $table->string('prenom');
            $table->string('nom');
            $table->string('fonction')->nullable();
            $table->string('societe');
            
            // Segmentation
            $table->string('secteur')->nullable();
            $table->string('localisation')->nullable();
            $table->string('taille_estimee')->nullable(); // startup, PME, ETI, grand groupe
            $table->string('type_entreprise')->nullable();
            
            // Canaux
            $table->string('linkedin')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            
            // Recherche stratégique
            $table->text('observations')->nullable();
            $table->string('signal_declencheur')->nullable(); // croissance, recrutement, projet...
            $table->text('hypotheses_organisationnelles')->nullable();
            $table->text('points_friction_probables')->nullable();
            
            // Préparation messages
            $table->string('angle_choisi')->nullable();
            $table->text('observation_utilisee')->nullable();
            $table->text('question_prevue')->nullable();
            $table->text('objection_probable')->nullable();
            
            // Pilotage
            $table->string('statut')->default('a_contacter'); // a_contacter, contacte, en_discussion, call_planifie, call_realise, proposition_envoyee, gagne, perdu, en_attente
            $table->string('canal_principal')->nullable(); // linkedin, email, telephone
            $table->string('prochaine_action')->nullable();
            $table->date('date_prochaine_action')->nullable();
            $table->string('niveau_interet')->default('neutre'); // neutre, friction_detectee, chaud
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
