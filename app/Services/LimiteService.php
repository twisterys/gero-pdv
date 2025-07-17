<?php

namespace App\Services;


use DB;


class LimiteService
{
    public static function generate_limite_session(){
        $limites_array= [];
        $limites = DB::table('limites')->get(['key','value'])->toArray();
        foreach ($limites as $limite){
            $limites_array[$limite->key] = $limite->value;
        }
        session()->put('limite',json_encode($limites_array));
    }

    public static function is_enabled($key){
        if (session()->has('limite')){
            $limite = json_decode(session()->get('limite'));
            return isset($limite->$key) && (bool)$limite->$key;
        }
        return false;
    }

    public static function get_value($key){
        if (session()->has('limite')){
            $limite = json_decode(session()->get('limite'));
            return $limite->$key ?? 0;
        }
        return 0;
    }

    public static function limite_avec_404($key) {
        if (!self::is_enabled($key)){
            return abort(404);
        }
    }
}
