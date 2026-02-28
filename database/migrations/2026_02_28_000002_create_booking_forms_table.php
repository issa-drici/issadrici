<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_forms', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->json('champs')->nullable(); // Champs personnalisés du formulaire
            $table->json('creneaux_disponibles')->nullable(); // Jours/heures disponibles
            $table->integer('duree_call')->default(30); // Durée en minutes
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_forms');
    }
};
