<?php

use App\Models\Semestre;
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
        Schema::create('facture_emises', function (Blueprint $table) {
            $table->id();
            $table->char('nom',255)->nullable();
            $table->char('prenom',255)->nullable();
            $table->char('numero_voie',255)->nullable();
            $table->char('rue',255)->nullable();
            $table->char('code_postal',5)->nullable();
            $table->char('ville',255)->nullable();
            $table->char('email',255)->nullable();
            $table->float('tva')->default(0);
            $table->float('prix')->default(0);
            $table->string('destinataire', 255);
            $table->date('date_creation')->nullable()->default(now());
            $table->string('nom_createur', 255);
            $table->date('date_paiement')->nullable();
            $table->date('date_due');
            $table->string('state', 1);
            $table->foreignId('semestre_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_emises');
    }
};
