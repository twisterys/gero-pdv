<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'article_id',
        'titre',
        'prix',
        'date_abonnement',
        'date_expiration',
        'description',
        'is_archived',
    ];


    protected static function booted()
    {
        static::creating(function ($abonnement) {
            // Si is_archived n'est pas défini, le définir par défaut à null
            if (is_null($abonnement->is_archived)) {
                $abonnement->is_archived = null;
            }
        });
    }
    // protected static function booted()
    // {
    //     static::creating(function ($abonnement) {
    //         // Si le status n'est pas défini, définir "active" par défaut
    //         if (empty($abonnement->status)) {
    //             $abonnement->status = 'active';
    //         }
    //     });
    // }

    // Relation avec le modèle Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation avec le modèle Article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }}
