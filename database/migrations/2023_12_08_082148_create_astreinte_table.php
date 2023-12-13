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
            //$table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('creneau_id')->constrained('creneaux')->onDelete('cascade');
            $table->enum('astreinte_type', AstreinteType::choices());
            $table->integer('note_deco')->default(0);
            $table->integer('note_orga')->default(0);
            $table->integer('note_anim')->default(0);
            $table->integer('note_menu')->default(0);
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('astreintes');
    }
};
