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
        Schema::create('sous_categorie_facture_recues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categorie_facture_recues')->onDelete('cascade');
            $table->string('code', 1);
            $table->string('nom', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sous_categorie_facture_recues');
    }
};
