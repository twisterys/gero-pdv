<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSession extends Model
{
    use HasFactory;

    protected $table = 'pos_sessions';
    protected $fillable = ['id', 'ouverte', 'magasin_id', 'user_id', 'date_fin', 'created_at', 'updated_at'];

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ventes()
    {
        return $this->hasMany(Vente::class, 'pos_session_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class,'pos_session_id');
    }
}
