<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Paiement extends Model
{
    use HasFactory;


    protected $fillable = [
        'payable_id',
        'payable_type',
        'client_id',
        'compte_id',
        'methode_paiement_key',
        'comptable',
        'date_paiement',
        'cheque_lcn_reference',
        'cheque_lcn_date',
        'note',
        'recu',
        'fournisseur_id',
        'decaisser',
        'encaisser',
        'pos_session_id',
        'magasin_id',
        'created_by'
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }

    public function methodePaiement()
    {
        return $this->belongsTo(MethodesPaiement::class, 'methode_paiement_key', 'key');
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function getDatePaiementAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }
}
