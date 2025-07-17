<?php

namespace App\Services;


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public const STORAGE_BASE_PATH = 'uploads';
    public const PUBLIC_BASE_PATH = 'public'.DIRECTORY_SEPARATOR.'uploads';
    public const TEMPORARY_DIRECTORY='temp';

    /**
     * Use this function to copy the ajax uploaded file from temp folder to main one in storage
     * @param string $name
     * @param string $new_path
     * @param string $path
     * @return bool|string
     */
    public static function filepondStoreStorageFile(string $name ,string $new_path ,string $path = self::TEMPORARY_DIRECTORY): bool|string
    {
        $path= $path.DIRECTORY_SEPARATOR.$name;
        if (Storage::fileExists($path)){
            $store_path = self::STORAGE_BASE_PATH.DIRECTORY_SEPARATOR.$new_path.$name;
            Storage::copy($path,$store_path);
            return $store_path;
        }
        return false;
    }

    /**
     * Use this function to copy the ajax uploaded file from temp folder to main one publicly
     * @param string $name
     * @param string $new_path
     * @param string $path
     * @return bool|string
     */
    public static function filepondStorePublicFile(string $name ,string $new_path ,string $path = self::TEMPORARY_DIRECTORY): bool|string
    {
        $path= $path.DIRECTORY_SEPARATOR.$name;
        if (Storage::fileExists($path)){
            $store_path = self::PUBLIC_BASE_PATH.DIRECTORY_SEPARATOR.$new_path.$name;
            Storage::copy($path,$store_path);
            return $store_path;
        }
        return false;
    }

    /**
     * This function will store the submitted file to the storage path
     * can be used by filepond upload too
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    public static function storageUploadFile(UploadedFile $file, string $path = self::TEMPORARY_DIRECTORY): string
    {
        $store_path = self::STORAGE_BASE_PATH . DIRECTORY_SEPARATOR . $path;
        $name = $file->getClientOriginalName();
        $name = time() . '-' . $name;
        $file->storeAs($store_path . DIRECTORY_SEPARATOR . $name);
        return $path.DIRECTORY_SEPARATOR.$name;
    }

    /**
     * This function will store the submitted file to the public path
     *  can be used by filepond upload too
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    public static function publicUploadFile(UploadedFile $file, string $path = self::TEMPORARY_DIRECTORY): string
    {
        $store_path = self::PUBLIC_BASE_PATH . DIRECTORY_SEPARATOR . $path;
        $name = $file->getClientOriginalName();
        $name = time() . '-' . $name;
        $file->storeAs($store_path . DIRECTORY_SEPARATOR . $name);
        return $name;
    }

    /**
     * Use this function to get the storage path fo your file
     * @param string $file
     * @return string
     */
    public static function getStoragePath(string $file,$path = 'temp'): string
    {
        return self::STORAGE_BASE_PATH.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file;
    }

    /**
     * Use this function to get the public path fo your file
     * @param string $file
     * @return string
     */
    public static function getPublicPath(string $file,$path = 'temp'): string
    {
        return 'storage'.DIRECTORY_SEPARATOR.self::STORAGE_BASE_PATH.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file;
    }

    /**
     * Use this function to get url of your public file
     * @param string $file
     * @return string
     */
    public static function getPublicUrl(string $file): string
    {
        return asset(explode(DIRECTORY_SEPARATOR,self::PUBLIC_BASE_PATH)[1].DIRECTORY_SEPARATOR.$file);
    }

    /**
     * @param $file
     * @param ResponseFactory $responseFactory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */

    public static function load($file)
    {
        $path = self::PUBLIC_BASE_PATH.DIRECTORY_SEPARATOR.$file;
        if (Storage::fileExists($path)) {
            return \response()->file(Storage::path($path),['Content-Type: image/*']);
        }
        return response('', 404);
    }




}
