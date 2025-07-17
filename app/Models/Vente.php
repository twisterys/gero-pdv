<?php

namespace App\Models;

use App\Services\GlobalService;
use App\Services\ModuleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vente extends Model
{
    use HasFactory;

    public const STATUTS_DE_PAIEMENT = [
        'non_paye',
        'partiellement_paye',
        'paye',
        'en_cours',
        'solde',
    ];
    protected $fillable = [
        'reference',
        'affaire_id',
        'commercial_id',
        'commission_par_defaut',
        'statut',
        'statut_com',
        'objet',
        'date_document',
        'date_expiration',
        "date_emission",
        'client_id',
        'total_ht',
        'total_tva',
        'total_reduction',
        'total_ttc',
        'type_document',
        'fichier_document',
        'solde',
        'encaisser',
        "created_by",
        'note',
        'statut_paiement',
        'piece_jointe',
        'magasin_id',
        'pos_session_id',
        'methode_livraison_id',
        'template_id',
        'is_controled',
        'controled_at',
        'controled_by'
    ];
    protected $casts = [
        "date_emission" => "date",
        "date_expiration" => "date"
    ];

    public const TYPES = [
        'dv',
        'bc',
        'bl',
        'br',
        'fa',
        'fp',
        'av',
    ];
    public static $status = [
        'brouillon',
        'validé',
    ];

    public static $statut_com = [
        'créé',
        'envoyé',
        'validé',
        'refusé'
    ];

    public const DECLENCHEUR = 'Vente';
    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function commercial()
    {
        return $this->belongsTo(Commercial::class);
    }
    public function lignes()
    {
        return $this->hasMany(VenteLigne::class);
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
        return $this->belongsToMany(Vente::class, 'ventes_relations', 'vente_id', 'en_relation_id');
    }

    public function document_parent()
    {
        return $this->belongsToMany(Vente::class, 'ventes_relations', 'en_relation_id', 'vente_id');
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

    public function magasin(){
        return $this->belongsTo(Magasin::class,'magasin_id');
    }

    public function promesses(){
        return $this->hasMany(Promesse::class);
    }

    public function solde_par()
    {
        return $this->belongsToMany(Vente::class,'vente_avoir','vente_id','avoir_id');
    }

    public function vente_solde()
    {
        return $this->belongsToMany(Vente::class,'vente_avoir','avoir_id','vente_id');
    }

    protected function joursDeRetard(): Attribute{
        return Attribute::make(
            get: fn($value,$attributes) => ($attributes['date_expiration'] ? Carbon::make($attributes['date_expiration'])->diffForHumans(Carbon::now(),true) : '0')
        );
    }

    public function template(){
        return $this->belongsTo(Template::class);
    }

    public function piecesJointes()
    {
        return $this->morphMany(PieceJointe::class, 'document');
    }

}
