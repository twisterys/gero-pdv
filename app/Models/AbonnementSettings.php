<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonnementSettings extends Model
{
    use HasFactory;

    protected $table ='abonnement_settings';

    protected $fillable = [
        'emails',    // List of emails separated by semicolons (;)
        'content',   // HTML content
        'subject',   // Email subject
        'notifier_client'
    ];

}
