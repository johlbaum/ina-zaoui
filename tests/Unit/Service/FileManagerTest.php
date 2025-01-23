<?php

namespace App\Tests\Service;

use App\Service\FileManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class FileManagerTest extends TestCase
{
    private $fileManager;
    private $kernelMock;

    protected function setUp(): void
    {
        $this->kernelMock = $this->createMock(KernelInterface::class);
        $this->kernelMock->method('getProjectDir')->willReturn('/myprojectdir'); // On simule le chemin absolu du répertoire racine du projet.

        $this->fileManager = new FileManager($this->kernelMock);
    }

    public function testGetFilePathWithUploadsPrefix(): void
    {
        // On vérifie qu'un chemin déjà préfixé par 'uploads/' reste inchangé.
        $filename = 'uploads/test_image.jpeg';
        $this->assertEquals('uploads/test_image.jpeg', $this->fileManager->getFilePath($filename));
    }

    public function testGetFilePathWithoutUploadsPrefix(): void
    {
        // On vérifie qu'un chemin sans préfixe 'uploads/' soit correctement préfixé.
        $filename = 'test_image.jpeg';
        $this->assertEquals('uploads/test_image.jpeg', $this->fileManager->getFilePath($filename));
    }

    public function testGetFilePathForTestEnvironment(): void
    {
        $this->kernelMock->method('getEnvironment')->willReturn('test');

        $filename = 'test_image.jpeg';

        $this->assertEquals('tests/Fixtures/images/test_image.jpeg', $this->fileManager->getFilePath($filename));
    }

    public function testGetFileDirectoryForDevEnvironment(): void
    {
        $this->kernelMock->method('getEnvironment')->willReturn('dev');

        $this->assertEquals('/myprojectdir/public/uploads/', $this->fileManager->getFileDirectory());
    }

    public function testGetFileDirectoryForTestEnvironment(): void
    {
        $this->kernelMock->method('getEnvironment')->willReturn('test');

        $this->assertEquals('/myprojectdir/tests/Fixtures/images/', $this->fileManager->getFileDirectory());
    }
}
