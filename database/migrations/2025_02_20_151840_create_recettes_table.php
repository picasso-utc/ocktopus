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
        Schema::create('recettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categorie_factures');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->float('valeur')->default(0);
            $table->float('tva')->default(0);
            $table->text('remarque')->nullable();
            $table->foreignId('semestre_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recettes');
    }
};
