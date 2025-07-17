<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationEntreprise extends Model
{

    protected $table = 'information_entreprise';

    protected $fillable = [
        'forme_juridique',
        'raison_social',
        'ice',
        'email',
        'telephone',
        'note',
        'RC',
        'IF',
        'adresse',
        'ville',
    ];
    public static function getFormJuridiqueTypes()
    {
        return [
            'sarl' => 'S.A.R.L.',
            'personne_physique' => 'Personne Physique',
            'auto_entrepreneur' => 'Auto Entrepreneur',
            'sa' => 'S.A.',

        ];
    }

}
