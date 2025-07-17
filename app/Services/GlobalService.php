<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\DB;

class GlobalService
{
   public static function get_all_globals(){
       return GlobalSetting::first();
   }

   public static function get_modifier_reference(){
       return GlobalSetting::first()->modifier_reference;
   }

    public static function get_code_barre(){
        return (bool) GlobalSetting::first()->code_barre ?? false;
    }
}
