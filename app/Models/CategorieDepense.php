<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieDepense extends Model
{
    use HasFactory;

    protected $table = 'categorie_depense';

    protected $fillable = [
        'id',
        'nom',
        'active',
    ];

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'categorie_depense_id');
    }
}
