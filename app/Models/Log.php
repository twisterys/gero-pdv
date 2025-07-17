<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use SoftDeletes;

    protected $fillable = ['tenant_id', 'message', 'etat'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
