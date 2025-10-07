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

    public static function get_decimal_length(): int
    {
        $gs = GlobalSetting::first();
        $len = $gs?->decimal_length ?? 2;
        // guardrails
        if (!is_numeric($len)) return 2;
        $len = (int) $len;
        if ($len < 0) $len = 0;
        if ($len > 5) $len = 5; // keep within migrated scale
        return $len;
    }

    public static function round_decimal(float|int $value): float
    {
        return round((float)$value, self::get_decimal_length());
    }
}
