<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Rebut extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_operation',
        'reference',
        'magasin_id',
        'pos_session_id',
        'statut',
    ];

    public const DECLENCHEUR = 'Rebut';

    protected $casts = [
        'date_operation' => 'date'
    ];

    public function magasin(): BelongsTo
    {
        return $this->belongsTo(Magasin::class);
    }

    public function transactions()
    {
        return $this->morphMany(TransactionStock::class,'stockable');
    }

    public function posSession(): BelongsTo
    {
        return $this->belongsTo(PosSession::class);
    }
    public function stock_transaction()
    {
        return $this->morphMany(TransactionStock::class, 'stockable');
    }
}
