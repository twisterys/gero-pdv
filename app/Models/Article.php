<?php

namespace App\Models;

use Attribute;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'designation',
        'reference',
        'description',
        'prix_achat',
        'prix_revient',
        'prix_vente',
        'stockable',
        'image',
        'unite_id',
        'famille_id',
        'taxe',
        'quantite_alerte',
        'marque_id',
        'numero_serie',
        'code_barre',
    ];

    public function famille()
    {
        return $this->belongsTo(Famille::class);
    }
    public function taxe()
    {
        return $this->belongsTo(Taxe::class);
    }
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }
    public function stock()
    {
        return $this->hasOne(Stock::class, 'article_id');
    }

    public function vente_lignes(): HasMany
    {
        return $this->hasMany(VenteLigne::class);
    }
    public function marque() {
        return $this->belongsTo(Marque::class,'marque_id');
    }

    public function magasin_stock($magasin_id)
    {
        return StockService::getMagasinStock($magasin_id, $this->id);
    }
    public function getQuantiteAttribute()
    {
        return $this->stock ? number_format($this->stock->quantite, 3, '.', '') : '0.000';
    }

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }
}
