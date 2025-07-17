<?php

namespace App\Services;

use App\Models\Module;

class ModuleService
{
    public static function getActiveModules(){
         $modules = Module::where('active',true)->pluck('type')->toArray();
        return  $modules;
    }

    public static function getModules(){
        return Module::all('type','active','action_stock','action_paiement');
    }
    public static function getPayabaleTypes(){
        $modules = Module::where('active',true)->whereNotNull('action_paiement')->pluck('type')->toArray();
        return  $modules;
    }
    public static function getEncaissementTypes(){
        $modules = Module::where('active',true)->where('action_paiement','encaisser')->pluck('type')->toArray();
        return  $modules;
    }
    public static function getDecaissementTypes(){
        $modules = Module::where('active',true)->where('action_paiement','decaisser')->pluck('type')->toArray();
        return  $modules;
    }
    public static function stockSortirTypes(){
        $modules = Module::where('active',true)->where('action_stock','sortir')->pluck('type')->toArray();
        return $modules;
    }
    public static function stockEntrerTypes(){
        $modules = Module::where('active',true)->where('action_stock','entrer')->pluck('type')->toArray();
        return $modules;
    }
}
