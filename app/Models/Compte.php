<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compte extends Model
{

    protected $casts = [
        'statut' => 'boolean',
        'principal' => 'boolean',
    ];

    protected $fillable = [
        'nom',
        'type',
        'banque_id',
        'rib',
        'adresse',
        'statut',
        'principal'
    ];

    public function paiements(){
        return $this->hasMany(Paiement::class);
    }

    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class);
    }

    public function getSoldeAttribute(){
        return $this->paiements()->selectRaw('sum(encaisser - decaisser) as solde')->first()->solde ?? 0;
    }
}
