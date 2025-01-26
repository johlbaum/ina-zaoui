<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class FileManager
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Retourne le chemin complet du fichier en fonction de l'environnement.
     *
     * @param string $filename : le nom du fichier
     * @return string : le chemin complet du fichier
     */
    public function getFilePath(string $filename): string
    {
        if ($this->kernel->getEnvironment() === 'test') {
            return 'tests/Fixtures/images/' . $filename;
        }

        // Si le fichier commence déjà par 'uploads/', on retourne ce chemin sans modification.
        if (strpos($filename, 'uploads/') === 0) {
            return $filename;
        }

        // Sinon, on ajoutee le préfixe 'uploads/'.
        return 'uploads/' . $filename;
    }

    /**
     * Retourne le répertoire où les fichiers doivent être enregistrés en fonction de l'environnement.
     *
     * @return string : le chemin du répertoire de destination
     */
    public function getFileDirectory(): string
    {
        if ($this->kernel->getEnvironment() === 'test') {
            return $this->kernel->getProjectDir() . '/tests/Fixtures/images/';
        }

        return $this->kernel->getProjectDir() . '/public/uploads/';
    }
}
