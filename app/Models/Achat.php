<?php

namespace App\Models;

use App\Services\GlobalService;
use App\Services\ModuleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achat extends Model
{
    use hasFactory;
    protected $fillable = [
        'reference',
        'reference_externe',
        'fournisseur_id',
        'objet',
        'statut',
        'type_document',
        'date_expiration',
        'date_emission',
        'fichier_document',
        'note',
        'statut_paiement',
        'piece_jointe',
        'total_ht',
        'total_tva',
        'total_reduction',
        'total_ttc',
        'debit',
        'credit',
        'created_by',
        'reference_interne',
        'magasin_id',
        'template_id',
        'is_controled',
        'controled_at',
        'controled_by',
        'jours_de_retard',
    ];
    use HasFactory;
    protected $casts = [
        "date_emission" => "date",
        "date_expiration" => "date"
    ];
    public static $status = [
        'brouillon',
        'validé',
    ];
    public const STATUTS_DE_PAIEMENT = [
        'non_paye',
        'partiellement_paye',
        'paye',
        'en_cours',
        'solde',
    ];

    public const TYPES = [
        'dva',
        'bca',
        'bla',
        'bra',
        'faa',
        'ava',
        'fpa'
    ];
    public const DECLENCHEUR = 'Achat';
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    public function lignes()
    {
        return $this->hasMany(AchatLigne::class);
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class, 'payable');
    }
    public function stock_transaction()
    {
        return $this->morphMany(TransactionStock::class, 'stockable');
    }
    public function documents_en_relation()
    {
        return $this->belongsToMany(Achat::class, 'achats_relations', 'achat_id', 'en_relation_id');
    }

    public function document_parent()
    {
        return $this->belongsToMany(Achat::class, 'achats_relations', 'en_relation_id', 'achat_id');
    }

    public function getDateExpirationAttribute($value)
    {
        // Assuming $value is the raw date from the database
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getDateEmissionAttribute($value)
    {
        // Assuming $value is the raw date from the database
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function template(){
        return $this->belongsTo(Template::class);
    }

    public function piecesJointes()
    {
        return $this->morphMany(PieceJointe::class, 'document');
    }
    protected function joursDeRetard(): Attribute{
        return Attribute::make(
            get: fn($value,$attributes) => ($attributes['date_expiration'] ? Carbon::make($attributes['date_expiration'])->diffForHumans(Carbon::now(),true) : '0')
        );
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

}
