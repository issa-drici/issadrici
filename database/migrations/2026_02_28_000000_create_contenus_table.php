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
        Schema::create('contenus', function (Blueprint $table) {
            $table->id();
            
            // Type de contenu
            $table->string('type'); // post, story, reel, article, video, carousel
            $table->string('titre')->nullable();
            $table->text('contenu');
            
            // Plateforme
            $table->string('plateforme'); // linkedin, instagram, facebook, twitter, tiktok, youtube
            
            // Statut et publication
            $table->string('statut')->default('brouillon'); // brouillon, planifie, publie, archive
            $table->dateTime('date_publication_planifiee')->nullable();
            $table->dateTime('date_publication_reelle')->nullable();
            $table->string('url_publication')->nullable();
            
            // Organisation
            $table->json('tags')->nullable(); // Tags/catégories
            $table->text('notes')->nullable();
            
            // Médias
            $table->string('image_url')->nullable();
            
            // Métriques
            $table->integer('engagement_estime')->nullable(); // Engagement estimé
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenus');
    }
};
