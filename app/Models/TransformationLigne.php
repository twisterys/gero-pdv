<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransformationLigne extends Model
{
    protected $fillable = [
        'nom_article',
        'article_id',
        'quantite',
        'type',
        'transformation_id',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function transformation(): BelongsTo
    {
        return $this->belongsTo(Transformation::class);
    }
}
