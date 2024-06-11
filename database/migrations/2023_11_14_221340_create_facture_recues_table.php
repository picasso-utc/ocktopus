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
        Schema::create('facture_recues', function (Blueprint $table) {
            $table->id();
            $table->string('state', 1);
            $table->float('tva')->default(0);
            $table->float('prix')->default(0);
            $table->char('destinataire',255);
            $table->date('date')->default(now());
            $table->date('date_paiement')->nullable();
            $table->date('date_remboursement')->nullable();
            $table->char('moyen_paiement',255)->nullable();
            $table->char('personne_a_rembourser',255)->nullable();
            $table->boolean('immobilisation')->default(False);
            $table->text('remarque')->nullable();
            $table->string('pdf_path')->nullable();
            $table->foreignId('semestre_id')->constrained();
            $table->char('facture_number',255)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_recues');
    }
};
