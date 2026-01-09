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
        Schema::create('signature_chartes', function (Blueprint $table) {
            $table->id();
            $table->string('adresse_mail');
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['adresse_mail', 'semestre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_chartes');
    }
};
