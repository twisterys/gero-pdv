<?php

namespace App\Services;


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PosService
{
    public static function getValue($key){
        return \DB::table('pos_settings')->where('key',$key)->first()->value ?? null;
    }
}
