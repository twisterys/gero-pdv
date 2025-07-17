<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransfertCaisse extends Model
{
    use HasFactory;

    protected $table = 'transfert_caisse';

    // Define the fillable properties to allow mass assignment
    protected $fillable = [
        'compte_source_id',
        'compte_destination_id',
        'date_emission',
        'date_reception',
        'montant',
        'description',
        'methode_paiement_key',
        'reference'
    ];

    /**
     * Get the source account for the transfer.
     */
    public function compteSource()
    {
        return $this->belongsTo(Compte::class, 'compte_source_id');
    }

    /**
     * Get the destination account for the transfer.
     */
    public function compteDestination()
    {
        return $this->belongsTo(Compte::class, 'compte_destination_id');
    }
}
