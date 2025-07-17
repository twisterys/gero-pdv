<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{

    const TYPES = [
        'telephone'=> 'Appel',
        'meeting'=>'Réunion'
    ];
    protected $fillable = [
        'titre', 'type', 'date', 'debut', 'fin', 'description', 'client_id'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
