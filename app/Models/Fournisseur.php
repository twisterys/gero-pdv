<?php

namespace App\Models;

use App\Services\GlobalService;
use App\Services\ModuleService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;
    protected $fillable = [
       'reference',
       'nom',
       'ice',
       'email',
       'telephone',
       'note',
       'limite_de_credit',
       'adresse',
       'forme_juridique_id',
       'rib',
    ];
    public static function getFormJuridiqueTypes()
    {
        return [
            'sarl' => 'S.A.R.L.',
            'personne_physique' => 'Personne Physique',
            'auto_entrepreneur' => 'Auto Entrepreneur',
            'sa' => 'S.A.',
            'sos' => 'SOS',
            'gie' => 'GIE',
            'snc' => 'SNC',
            'scs' => 'SCS',
            'sca' => 'SCA',
        ];
    }
    public function getFormJuridiqueLabelAttribute()
    {
        $types = self::getFormJuridiqueTypes();
        return $types[$this->forme_juridique] ?? '';
    }

    public function forme_juridique(){
        return $this->belongsTo(FormeJuridique::class, 'forme_juridique_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'fournisseur_contact');
    }
    public function achats()
    {
        return $this->hasMany(Achat::class);
    }
    public function achatsImpaye()
    {
        return $this->hasMany(Achat::class)->where('statut','validé')->whereIn('type_document',ModuleService::getPayabaleTypes())->whereNot('debit',0);
    }
}
