<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_form_id')->constrained()->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->dateTime('date_expiration')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_links');
    }
};
