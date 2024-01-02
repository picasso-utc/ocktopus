<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Treso\MontantCategorie;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facture_recues', function (Blueprint $table) {
            $table->id();
            $table->string('state', 1);
            $table->float('tva')->default(0);
            $table->float('prix')->default(0);
            $table->char('nom_entreprise',255);
            $table->date('date')->default(now());
            $table->date('date_paiement')->nullable();
            $table->date('date_remboursement')->nullable();
            $table->char('moyen_paiement',255)->nullable();
            $table->char('personne_a_rembourser',255)->nullable();
            $table->boolean('immobilisation')->default(False);
            $table->text('remarque')->nullable();
            $table->text('facture_number')->nullable();

            //  Attribut pour link une facture à une perm
            //$table->foreignId('perm')->references('perms','creneau')->onDelete('cascade');

            //  Attribut pour link une facture à un semestre
            //$table->foreignId('semestre')->nullable()->references('semestre','semestre_id')
            //    ->onDelete('set null');;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_recues');
    }
};
