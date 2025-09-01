<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    public const STATUTS_DE_PAIEMENT = [
        'non_paye',
        'partiellement_paye',
        'paye' ,
    ];

    protected $fillable = [
        'id',
        'reference',
        'nom_depense',
        'categorie_depense_id',
        'pour',
        'montant',
        'date_operation',
        'description',
        'solde',
        'encaisser',
        'statut_paiement',
        'pos_session_id',
        'taxe',
        'affaire_id',
        'magasin_id'
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class,'payable');
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
