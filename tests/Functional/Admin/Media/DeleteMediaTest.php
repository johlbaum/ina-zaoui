<?php

namespace App\Tests\Functional\Admin\Media;

use App\Entity\Media;
use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteMediaTest extends FunctionalTestCase
{
    public function testShouldDeleteMediaByAdmin(): void
    {
        // On indique le chemin vers le fichier source que l'on souhaite dupliquer.
        $sourceFile = self::getContainer()->getParameter('kernel.project_dir').'/tests/Fixtures/images/test_file.jpeg';

        // On définit où le fichier temporaire sera stocké et quel nom il aura.
        $tempFile = self::getContainer()->getParameter('kernel.project_dir').'/tests/Fixtures/images/test_file_temp.jpeg';

        // On copie le fichier source dans le répertoire de test.
        copy($sourceFile, $tempFile);

        // On vérifie que le fichier temporaire existe après la copie.
        $this->assertFileExists($tempFile);

        // On ajoute une entité Media dans la base de données.
        $media = new Media();
        $media->setPath('test_file_temp.jpeg');
        $media->setTitle('Media delete test');
        $media->setUser($this->entityManager->getRepository(User::class)->findOneByEmail('ina@zaoui.com'));
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        // On vérifie que le média a bien été créé en base de données.
        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Media delete test']);
        $this->assertNotNull($media);

        $this->login();

        $this->delete('/admin/media/delete/'.$media->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $deletedAlbum = $this->entityManager->getRepository(Media::class)->findOneBy(['id' => $media->getId()]);

        $this->assertNull($deletedAlbum);
    }

    public function testShouldDeleteMediaByGuest(): void
    {
        // On indique le chemin vers le fichier source que l'on souhaite dupliquer.
        $sourceFile = self::getContainer()->getParameter('kernel.project_dir').'/tests/Fixtures/images/test_file.jpeg';

        // On définit où le fichier temporaire sera stocké et quel nom il aura.
        $tempFile = self::getContainer()->getParameter('kernel.project_dir').'/tests/Fixtures/images/test_file_temp.jpeg';

        // On copie le fichier source dans le répertoire de test.
        copy($sourceFile, $tempFile);

        // On vérifie que le fichier temporaire existe après la copie.
        $this->assertFileExists($tempFile);

        // On ajoute une entité Media dans la base de données.
        $media = new Media();
        $media->setPath('test_file_temp.jpeg');
        $media->setTitle('Media delete test');
        $media->setUser($this->entityManager->getRepository(User::class)->findOneByEmail('ina@zaoui.com'));
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        // On vérifie que le média a bien été créé en base de données.
        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Media delete test']);
        $this->assertNotNull($media);

        $this->login('invite1@example.com');

        $this->delete('/admin/media/delete/'.$media->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $deletedAlbum = $this->entityManager->getRepository(Media::class)->findOneBy(['id' => $media->getId()]);

        $this->assertNull($deletedAlbum);
    }
}
