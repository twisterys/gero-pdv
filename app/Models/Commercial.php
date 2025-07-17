<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commercial extends Model
{
    use HasFactory;

    protected $fillable = [

        'reference',
        'nom',
        'ice',
        'email',
        'telephone',
        'note',
        'secteur',
        "objectif",
        "commission_par_defaut",
        "type_commercial",
        "image"
    ];
    public static function type_de_commercial()
    {
        return [
            "externe" => "Externe",
            "interne" => "Interne"
        ];
    }
    public function getType_de_commercialAttribute()
    {
        $types = self::type_de_commercial();
        return $types[$this->type_commercial] ?? '';
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class, 'commercial_id')->where('statut', 'validé');
    }
}
