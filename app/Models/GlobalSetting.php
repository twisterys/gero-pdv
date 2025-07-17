<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
       'modifier_reference',
        'prix_revient',
        'dashboard_date',
        'template_par_document',
        'code_barre',
        'dashboard',
        'controle',
        'pieces_jointes'
    ];
}
