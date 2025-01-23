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


    public function getFileDirectory(): string
    {
        if ($this->kernel->getEnvironment() === 'test') {
            return $this->kernel->getProjectDir() . '/tests/Fixtures/images/';
        }

        return $this->kernel->getProjectDir() . '/public/uploads/';
    }
}
