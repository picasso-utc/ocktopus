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
        Schema::create('montant_categorie', function (Blueprint $table) {
            $table->id();
            $table->float('prix')->default(0);
            $table->float('tva')->default(0);
            $table->foreignId('categorie_id')->constrained('categorie_factures')->onDelete('cascade');
            $table->foreignId('facture_id')->constrained('facture_recues')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('montant_categorie');
    }
};
