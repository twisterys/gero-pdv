<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransfertLigne extends Model
{
    use HasFactory;
    protected $fillable = [
        'transfert_id',
        'article_id',
        'description',
        'qte',
    ];

    public function transfert()
    {
        return $this->belongsTo(Transfert::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
