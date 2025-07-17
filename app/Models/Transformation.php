<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transformation extends Model
{
    public const DECLENCHEUR = 'Transformation';
    protected $fillable = [
        'reference',
        'date',
        'object',
        'note',
        'magasin_id',
        'created_by',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function magasin(): BelongsTo
    {
        return $this->belongsTo(Magasin::class,'magasin_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lignes(){
        return $this->hasMany(TransformationLigne::class);
    }

    public function getDateAttribute($value){
        return Carbon::make($value)->format('d/m/Y');
    }

    public function stock_transaction(){
        return $this->morphMany(TransactionStock::class, 'stockable');
    }
}
