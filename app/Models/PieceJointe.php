<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PieceJointe extends Model
{
    protected $table = 'pieces_jointes';
    protected $fillable = [
        'title',
        'url',
        'document_id',
        'document_type',
    ];

    public function document()
    {
        return $this->morphTo();
    }
}
