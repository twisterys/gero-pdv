<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reference',
        'titre',
        'description',
        'statut',
        'budget_estimatif',
        'ca_global',
        'date_debut',
        'date_fin',
        'cycle_type',
        'cycle_duree',
    ];

    /**
     * Get the client that owns the affaire.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function depenses(){
        return $this->hasMany(Depense::class);
    }

    /**
     * Get the jalons for the affaire.
     */
    public function jalons()
    {
        return $this->hasMany(Jalon::class);
    }
    public function getDateDebutAttribute($value)
    {
        // Assuming $value is the raw date from the database
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getDateFinAttribute($value)
    {
        // Assuming $value is the raw date from the database
        return Carbon::parse($value)->format('d/m/Y');
    }
}
