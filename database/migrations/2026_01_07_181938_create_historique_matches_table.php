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
        Schema::create('historique_matches', function (Blueprint $table) {
            $table->id();
            $table->string('mail_envoyeur', 255)->nullable();
            $table->string('nom_envoyeur', 255)->nullable();
            $table->string('mail_receveur', 255)->nullable();
            $table->string('nom_receveur', 255)->nullable();
            $table->enum('type', ['babyfoot', 'billard']);
            $table->boolean('gagner');
            $table->boolean('valider')->default(false);
            $table->integer('score_envoyeur')->nullable();
            $table->integer('score_receveur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_matches');
    }
};
