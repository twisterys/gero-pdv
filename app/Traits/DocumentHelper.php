<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;

trait DocumentHelper
{
    function store_document_image($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = 'public'.DIRECTORY_SEPARATOR. 'documents'.DIRECTORY_SEPARATOR.$fileName;
        Storage::disk('external_storage')->put($path, file_get_contents($file));
        return $fileName;
    }
    function get_image_url(string $image_name)
    {
        $path = 'public/documents/'.$image_name;
        return asset($path);
    }
    public  function load($file)
    {
        $path = 'public/documents/'.$file;
        if (Storage::disk('external_storage')->exists($path)) {
            return response()->file(Storage::disk('external_storage')->path($path), ['Content-Type' => 'image/*']);
        }
        return response('', 404);
    }

}
