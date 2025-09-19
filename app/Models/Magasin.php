<?php

namespace App\Models;

use App\Services\PosService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    use HasFactory;

    protected $table = 'magasins';

    public const TYPE_lOCAL = [
        1 =>'Point de vente & dépôt',
        2 =>'Dépôt seulement',
    ];

    public function getTypeLocalNameAttribute()
    {
        return self::TYPE_lOCAL[$this->attributes['type_local']] ?? 'Unknown';
    }

    protected $fillable = [
        'reference',
        'nom',
        'adresse',
        'type_local',
        'compte_id',
    ];

    public function ventes(){
        $type = PosService::getValue('type_vente');
        return $this->hasMany(Vente::class)->whereNotNull('pos_session_id')->where('type_document',$type);
    }
    public function retours(){
        $type = PosService::getValue('type_retour');
        return $this->hasMany(Vente::class)->whereNotNull('pos_session_id')->where('type_document',$type);
    }

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id');
    }


}
