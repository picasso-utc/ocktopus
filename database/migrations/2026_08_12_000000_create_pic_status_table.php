<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pic_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('open')->default(false);
            $table->timestamps();
        });

        DB::table('pic_status')->insert([
            'id' => 1,
            'open' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pic_status');
    }
};
