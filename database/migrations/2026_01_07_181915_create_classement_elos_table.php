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
        Schema::create('classement_elos', function (Blueprint $table) {
            $table->id();
            $table->string('mail_user', 255)->nullable();
            $table->string('nom_user', 255)->nullable();
            $table->integer('elo_score')->default(1000);
            $table->enum('type', ['babyfoot', 'billard']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classement_elos');
    }
};
