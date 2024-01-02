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
        Schema::create('facture_emises', function (Blueprint $table) {
            $table->id();
            $table->float('tva')->default(0);
            $table->float('prix')->default(0);
            $table->string('destinataire', 255);
            $table->date('date_creation')->nullable()->default(now());
            $table->string('nom_createur', 255);
            $table->date('date_paiement')->nullable();
            $table->date('date_due');
            $table->enum('etat', ['D', 'A', 'T', 'P']);
            //$table->foreignId('semestre')->nullable->references('semestre_id')->on('Semestre')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_emises');
    }
};
