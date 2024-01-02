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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->integer('num');
            $table->float('valeur')->default(0);

            // Utilisation d'une colonne ENUM pour 'state'
            $table->enum('state', ['E', 'P', 'A', 'C']);
            $table->string('destinataire', 255)->nullable()->default(null);
            $table->date('date_encaissement')->nullable();
            $table->date('date_emission')->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('facture_id')->nullable()->constrained('facture_recues','id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
