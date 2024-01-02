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
        Schema::create('note_de_frais', function (Blueprint $table) {
            $table->id();
            $table->string('state', 1);
            $table->date('date_facturation')->default(now());
            $table->char('nom',255);
            $table->char('prenom',255);
            $table->char('numero_voie',255);
            $table->char('rue',255);
            $table->char('code_postal',5);
            $table->char('ville',255);
            $table->char('email',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_de_frais');
    }
};
