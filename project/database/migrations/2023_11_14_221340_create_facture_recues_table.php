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
            $table->enum('sate', ['D', 'R','E','P'])->default('D');
            $table->float('tva')->default(0);
            $table->float('prix')->default(0);
            $table->foreignId('perm')->references('Perms','creneau')->onDelete('cascade');
            $table->char('nom_entreprise',255);
            $table->date('date');
            $table->dateTime('date_created')->nullable()->default(now());
            $table->date('date_paiement')->nullable();
            $table->date('date_remboursement')->nullable();
            $table->char('moyen_paiement',255)->nullable();
            $table->char('personne_a_rembourser',255)->nullable();
            $table->boolean('immobilisation')->default(False);
            $table->text('remarque')->nullable();
            $table->foreignId('semestre')->nullable()->references('Semestre','semestre_id')
                ->onDelete('set null');;
            $table->text('facture_number')->nullable();
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
