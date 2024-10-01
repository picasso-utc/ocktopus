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
        Schema::create('extes', function (Blueprint $table) {
            $table->id();
            $table->string('etu_nom_prenom');
            $table->string('etu_cas');
            $table->string('etu_mail');
            $table->string('exte_nom_prenom');
            $table->date('exte_date_debut');
            $table->date('exte_date_fin');
            $table->boolean('responsabilite')->default(false);
            $table->string('commentaire')->nullable();
            $table->boolean('mailed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extes');
    }
};
