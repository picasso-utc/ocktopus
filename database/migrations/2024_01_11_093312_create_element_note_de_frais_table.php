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
        Schema::create('element_note_de_frais', function (Blueprint $table) {
            $table->id();
            $table->char('description',255);
            $table->float('tva')->default(0);
            $table->float('prix_unitaire_ttc')->default(0);
            $table->integer('quantite')->nullable();
            $table->foreignId('note_de_frais_id')->constrained('note_de_frais')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_note_de_frais');
    }
};
