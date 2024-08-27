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
        Schema::table('perms', function (Blueprint $table) {
            // Ajout de la colonne 'jour' qui peut contenir plusieurs jours (donc json)
            $table->json('jour')->nullable();
            $table->string('repas')->nullable();
            $table->string('idea_repas')->nullable();
            $table->string('remarques')->nullable();
            $table->boolean('teddy')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perms', function (Blueprint $table) {
            $table->dropColumn(['jour','repas','idea_repas', 'remarques','teddy']);
        });
    }
};
