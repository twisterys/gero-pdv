<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compteur extends Model
{
    use HasFactory;

    const TYPES = [
        'clt'=>'Clients',
        'fr'=>'Fournisseurs',
        'cms'=>'Commerciaux',
        'art'=>'Articles',
        'dv'=>'Devis',
        'fa'=>'Facture',
        'fp'=>'Facture proforma',
        'av'=>'Avoir',
        'bl'=>'Bon de livraison',
        'bc'=>'Bon de commande',
        'br'=>'Bon de retour',
        'dva'=>"Devis d'achat",
        'bca'=>'Bon de commande d\'achat',
        'bla'=>'Bon de livraison d\'achat',
        'bra'=>'Bon de retour d\'achat',
        'faa'=>'Facture d\'achat',
        'ava'=>'Avoir d\'achat',
        'dpa'=>'Depense',
        'fpa'=>'Facture proforma d\'achat',
    ];

    protected $fillable = [
        'type',
        'annee',
        'compteur'
    ];
}
