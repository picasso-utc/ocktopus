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
        Schema::table('facture_recues', function (Blueprint $table) {
            $table->boolean('signed')->default(false)->after('state'); // Place le champ 'signed' après 'state'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facture_recues', function (Blueprint $table) {
            $table->dropColumn('signed');
        });
    }
};
