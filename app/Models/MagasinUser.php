<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagasinUser extends Model
{
    use HasFactory;
    protected $table = 'magasin_user';
    protected $fillable = [
        'user_id',
        'magasin_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
}
