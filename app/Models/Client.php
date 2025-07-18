<?php

namespace App\Models;

use App\Services\GlobalService;
use App\Services\ModuleService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'forme_juridique',
        'reference',
        'nom',
        'ice',
        'email',
        'telephone',
        'note',
        'limite_de_credit',
        'limite_ventes_impayees',
        'adresse',
        'forme_juridique_id',
        'ville',
        'remise_par_defaut'
    ];
    public static function getFormJuridiqueTypes()
    {
        return [
            'sarl' => 'S.A.R.L.',
            'personne_physique' => 'Personne Physique',
            'auto_entrepreneur' => 'Auto Entrepreneur',
            'sa' => 'S.A.',
            // 'sos' => 'SOS',
            // 'gie' => 'GIE',
            // 'snc' => 'SNC',
            // 'scs' => 'SCS',
            // 'sca' => 'SCA',
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

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'client_contact');
    }
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }
    public function ventesImpaye()
    {
        $payable_types = ModuleService::getPayabaleTypes();
        return $this->hasMany(Vente::class)->where('statut','validé')->whereIn('type_document',$payable_types)->whereNot('solde',0);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }
}
