<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
      'id',
      'article_id',
      'quantite'
    ];
    public const DECLENCHEUR = 'Ouverture';

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
