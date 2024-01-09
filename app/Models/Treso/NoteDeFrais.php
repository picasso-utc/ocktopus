<?php

namespace App\Models\Treso;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteDeFrais extends Model
{
    use HasFactory;

    protected $table = 'note_de_frais';
    protected $fillable = [
        'state', 'date_facturation', 'nom', 'prenom', 'numero_voie', 'rue',
        'code_postal', 'ville', 'email'
    ];

    public function getStateLabel(string $etat)
    {
        if ($etat === 'D') {
            return 'Note à payer';
        } elseif ($etat === 'R') {
            return 'Note à rembourser';
        } elseif ($etat === 'E') {
            return 'Note en attente';
        } elseif ($etat === 'P') {
            return 'Note payée';
        } else {
            return 'Inconnu';
        }
    }

    public function elementFacture()
    {
        return $this->hasMany(ElementFacture::class, 'note_de_frais_id');
    }
}
