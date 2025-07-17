<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;

trait ImportationHelper
{
    function store_import_file($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = 'public'.DIRECTORY_SEPARATOR. 'importations'.DIRECTORY_SEPARATOR.$fileName;
        Storage::disk('external_storage')->put($path, file_get_contents($file));
        return $fileName;
    }


    public  function load($file)
    {
        $path = 'public/importations/'.$file;
        if (Storage::disk('external_storage')->exists($path)) {

            return response()->download(Storage::disk('external_storage')->path($path),  $file, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'inline; filename="' . $path . '"'
            ]);        }
        return response('', 404);
    }
}
