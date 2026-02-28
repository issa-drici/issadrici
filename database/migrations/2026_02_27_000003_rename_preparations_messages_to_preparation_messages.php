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
        // La table est créée sous le nom "preparation_messages" dans 000001.
        // Ce renommage ne s'applique que si une ancienne base avait "preparations_messages".
        if (Schema::hasTable('preparations_messages') && !Schema::hasTable('preparation_messages')) {
            Schema::rename('preparations_messages', 'preparation_messages');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('preparation_messages')) {
            Schema::rename('preparation_messages', 'preparations_messages');
        }
    }
};
