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
        Schema::create('shotgun', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->foreignId('events_id')->constrained();
            $table->timestamps();
            $table->unique(['events_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shotgun');
    }
};
