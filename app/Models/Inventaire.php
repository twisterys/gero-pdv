<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'fichier_path',
        'magasin_id',
        'type',
        'date',
        'statut',
        'reference',
        'type_inventaire'
    ];
    public const DECLENCHEUR = 'Inventaire';

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function transactions()
    {
        return $this->morphMany(TransactionStock::class,'stockable');
    }

    public function stock_transaction()
    {
        return $this->morphMany(TransactionStock::class, 'stockable');
    }
}
