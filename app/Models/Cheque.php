<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'type',
        'number',
        'montant',
        'date_emission',
        'date_echeance',
        'statut',
        'banque_id',
        'client_id',
        'compte_id',
        'fournisseur_id',
        'note'
    ];

    public function banque()
    {
        return $this->belongsTo(Banque::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    const TYPES = ['encaissement', 'decaissement'];
    const STATUTS = ['en_attente', 'traite', 'annule'];

}
