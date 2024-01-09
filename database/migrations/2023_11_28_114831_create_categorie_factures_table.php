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
        Schema::create('categorie_factures', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255)->unique();
            $table->bigInteger('parent_id')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categorie_factures')
                ->onDelete('cascade')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie_factures');
    }
};
