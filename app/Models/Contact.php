<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;


    protected $fillable = ['nom', 'prenom', 'email', 'telephone','is_principal'];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_contact');
    }
}
