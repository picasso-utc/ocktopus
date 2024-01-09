<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FactureRecue extends Model
{
    use HasFactory;

    protected $table ='facture_recues';
    protected $fillable = [
        'tva', 'prix', 'perm_id', 'state', 'destinataire', 'date',
        'date_created', 'date_paiement', 'date_remboursement', 'moyen_paiement',
        'personne_a_rembourser', 'immobilisation', 'remarque', 'semestre_id', 'facture_number','pdf_path'
    ];

    public function getStateLabel(string $etat)
    {
        if ($etat === 'D') {
            return 'Facture à payer';
        } elseif ($etat === 'R') {
            return 'Facture à rembourser';
        } elseif ($etat === 'E') {
            return 'Facture en attente';
        } elseif ($etat === 'P') {
            return 'Facture payée';
        } else {
            return 'Inconnu';
        }
    }

    /*
    public function perm()
    {
        return $this->belongsTo(
            Creneau::class, 'perm_id');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'semestre_id');
    }
    */

    public function getTotalPrice()
    {
        $price = 0;

        foreach ($this->categoriePrix as $cat){
            $price += $cat->prix;
        }

        return $price;
    }

    public function getTotalTaxes()
    {
        return round($this->prix - $this->getPriceWithoutTaxes(), 2);
    }

    public function categoriePrix(): HasMany
    {
        return $this->hasMany(MontantCategorie::class, 'facture_id');
    }


}
