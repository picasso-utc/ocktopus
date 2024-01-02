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
        Schema::create('creneau', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perm_id')->nullable()
                ->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('creneau', ['M','D','S']);
            $table->timestamps();

            $table->unique(['date', 'creneau']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creneau');
    }
};
