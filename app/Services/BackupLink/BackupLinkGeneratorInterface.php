<?php

namespace App\Services\BackupLink;

interface BackupLinkGeneratorInterface
{
    /**
     * Génère un lien de téléchargement/accès pour un fichier de backup.
     *
     * @param string $filePath Le chemin du fichier sur le disque de stockage.
     * @param array $storageConfig La configuration du disque.
     * @return string Le lien généré.
     * @throws \Exception si la génération échoue.
     */
    public function generate(string $filePath, array $storageConfig): string;
}
