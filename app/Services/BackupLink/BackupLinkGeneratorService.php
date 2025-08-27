<?php

namespace App\Services\BackupLink;

use InvalidArgumentException;

class BackupLinkGeneratorService
{
    /**
     * Retourne l'instance du générateur de lien approprié pour un driver donné.
     *
     * @param string $driver Le nom du driver (ex: 'google', 's3').
     * @return BackupLinkGeneratorInterface
     */
    public function getGeneratorFor(string $driver): BackupLinkGeneratorInterface
    {
        return match (strtolower($driver)) {
            'google' => new GoogleDriveLinkGenerator(),
            // 's3' => new S3LinkGenerator(), // À ajouter plus tard
            default => throw new InvalidArgumentException("Le driver de backup '{$driver}' n'est pas supporté pour la génération de lien."),
        };
    }
}
