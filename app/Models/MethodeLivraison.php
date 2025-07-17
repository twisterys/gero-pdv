<?php

namespace App\Models;

use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MethodeLivraison extends Model
{
    use HasFactory;


    protected $fillable = [
        'nom',
        'image',
    ];

     function image_url(){
         $path = 'public/methodes-livraison/' . $this->image;
         if (Storage::disk('external_storage')->exists($path)) {
             $type = pathinfo($this->image, PATHINFO_EXTENSION);
             $data = Storage::disk('external_storage')->get($path);
             $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
             return $base64;
         }
         return null;
    }
}
