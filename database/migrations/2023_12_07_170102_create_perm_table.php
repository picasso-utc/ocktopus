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
        Schema::create('perms', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->string('theme', 255);
            $table->text('description')->nullable();
            $table->text('periode')->nullable();
            $table->text('membres')->nullable();
            $table->integer('ambiance')->default(0);
            $table->boolean('asso')->default(true);
            $table->string('nom_resp', 255)->nullable();
            $table->string('mail_resp', 255)->nullable();
            $table->string('nom_resp_2', 255)->nullable();
            $table->string('mail_resp_2', 255)->nullable();
            $table->string('mail_asso', 255)->nullable();
            $table->boolean('validated')->default(false);
            $table->foreignId('semestre_id')->constrained();
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
        Schema::dropIfExists('perms');
    }
};
