<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelanceSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'content',
        'emails_cc',
        'subject',
        'type'
    ];

    /**
     * Get the active record where `active` is true.
     *
     * @return RelanceSettings|null
     */
    public static function getActive(String $type)
    {
      return self::where('active', true)->where('type', $type)->first();
    }
}
