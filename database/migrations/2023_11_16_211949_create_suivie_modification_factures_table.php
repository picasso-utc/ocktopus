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
        Schema::create('suivie_modification_factures', function (Blueprint $table) {
            $table->id();
            $table->text('facture_number')->nullable();
            $table->char('action', 1);
            $table->string('login', 16);
            $table->dateTime('date_creation')->nullable()->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivie_modification_factures');
    }
};
