<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WoocommerceImport extends Model
{
    protected $fillable = [
        'type',
        'statut',
        'reference',
        'last_imported_object',
        'magasin_id',
    ];

    protected $casts = [
        'last_imported_object' => 'timestamp',
    ];

    public function magasin(): BelongsTo
    {
        return $this->belongsTo(Magasin::class);
    }
}
