<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creneaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perm_id')->nullable()
                ->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('creneau', ['M','D','S']);
            $table->enum('state', ['T','N'])->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creneaux');
    }
};
