<?php

namespace App\Tests\Functional\Admin\Media;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddMediaTest extends FunctionalTestCase
{
    public function testShouldAddMediaByAdmin(): void
    {
        $this->login();

        $crawler = $this->get('/admin/media/add');
        $this->assertResponseIsSuccessful();

        $userRepository = $this->entityManager->getRepository(User::class);
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $user = $userRepository->find(1);
        $album = $albumRepository->find(1);

        $form = $crawler->filter('form')->form();
        $form['media[user]'] = $user->getId();
        $form['media[album]'] = $album->getId();
        $form['media[title]'] = 'Un média de test';
        $form['media[file]'] = __DIR__.'/../../../Fixtures/images/test_file.jpeg';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Un média de test']);

        $this->assertNotNull($media);
        $this->assertEquals($user->getId(), $media->getUser()->getId());
        $this->assertEquals($album->getId(), $media->getAlbum()->getId());
    }

    public function testShouldAddMediaByGuest(): void
    {
        $this->login('invite1@example.com');

        $crawler = $this->get('/admin/media/add');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $form['media[title]'] = 'Un média de test';
        $form['media[file]'] = __DIR__.'/../../../Fixtures/images/test_file.jpeg';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Un média de test']);
        $guest = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'invite1@example.com']);

        $this->assertNotNull($media);
        $this->assertEquals($guest->getId(), $media->getUser()->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Un média de test']);

        // On supprime le fichier.
        if ($media && file_exists($media->getPath())) {
            unlink($media->getPath());
        }

        // On supprime le média de la base de données.
        if ($media) {
            $this->entityManager->remove($media);
            $this->entityManager->flush();
        }
    }
}
