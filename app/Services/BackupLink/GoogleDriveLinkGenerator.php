<?php

namespace App\Services\BackupLink;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
class GoogleDriveLinkGenerator implements BackupLinkGeneratorInterface
{
    public function generate(string $filePath, array $storageConfig): string
    {
        $diskName = 'google_storage';

        config(['filesystems.disks.' . $diskName => $storageConfig]);

        try {
            $disk = Storage::disk($diskName);

            $link = $disk->url($filePath);

            if (empty($link)) {
                throw new \Exception("La méthode url() n'a pas retourné de lien. Le fichier '{$filePath}' existe-t-il bien sur Google Drive ?");
            }

            return $link;

        } catch (FileNotFoundException $e) {
            throw new \Exception("Le fichier de backup '{$filePath}' n'a pas été trouvé sur le disque Google Drive.", 0, $e);
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la génération du lien Google Drive pour '{$filePath}': " . $e->getMessage(), 0, $e);
        }
    }
}
