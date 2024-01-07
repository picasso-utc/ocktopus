<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AstreinteType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('astreintes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id');
            $table->foreignId('creneau_id');
            $table->enum('astreinte_type', AstreinteType::choices());
            $table->integer('note_deco')->nullable();
            $table->integer('note_orga')->nullable();
            $table->integer('note_anim')->nullable();
            $table->integer('note_menu')->nullable();
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('astreintes');
    }
};
