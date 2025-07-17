<?php

namespace App\Traits;
use App\Models\Magasin;

trait HasMagasinPermission
{

    function magasins(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Magasin::class,'magasin_user');
    }
   function accessibleTo($magasin): bool
   {
       return session()->has('accessibleMagasinsSession') && in_array($magasin, json_decode(session()->get('accessibleMagasinsSession')));
   }
   function generateAccessibleMagasinsSession() {
        session()->put('accessibleMagasinsSession',json_encode($this->magasins->pluck('id')->toArray()));
   }
   function assign_magasin($magasin){
        $this->magasins()->attach($magasin);
        $this->generateAccessibleMagasinsSession();
   }
}
