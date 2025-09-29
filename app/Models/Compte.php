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

    public function magasins()
    {
        return $this->belongsToMany(Magasin::class, 'compte_magasin', 'compte_id', 'magasin_id');
    }

    public function getSoldeAttribute(){
        return $this->paiements()->selectRaw('sum(encaisser - decaisser) as solde')->first()->solde ?? 0;
    }

    static function ofUser($user = null)
    {
        $user = $user ?: request()->user();
        if (!$user) {
            return static::query()->whereRaw('1 = 0');
        }
        $magasinIds = $user->magasins()->pluck('magasins.id');

        return static::query()
            ->whereHas('magasins', function ($q) use ($magasinIds) {
                $q->whereIn('magasins.id', $magasinIds);
            })
            ->distinct();
    }
}
