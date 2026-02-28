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
        Schema::table('contenus', function (Blueprint $table) {
            $table->string('angle')->nullable()->after('titre');
            $table->string('cible')->nullable()->after('angle');
            $table->text('probleme_vise')->nullable()->after('cible');
            $table->text('solution_proposee')->nullable()->after('probleme_vise');
            $table->text('objectif_contenu')->nullable()->after('solution_proposee');
            $table->text('call_to_action')->nullable()->after('objectif_contenu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contenus', function (Blueprint $table) {
            $table->dropColumn([
                'angle',
                'cible',
                'probleme_vise',
                'solution_proposee',
                'objectif_contenu',
                'call_to_action',
            ]);
        });
    }
};
