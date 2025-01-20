<?php

namespace App\Tests\Functional\Media;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;

class AddMediaTest extends FunctionalTestCase
{
    public function testShouldAddMediaByAdmin(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On envoie une requête GET pour accéder au formulaire d'ajout de média.
        $crawler = $this->get('/admin/media/add');
        $this->assertResponseIsSuccessful();

        // On récupère l'utilisateur et un album depuis la base de données.
        $userRepository = $this->entityManager->getRepository(User::class);
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $user = $userRepository->find(1);
        $album = $albumRepository->find(1);

        // On vérifie que le formulaire d'ajout est bien présent.
        $form = $crawler->filter('form')->form();

        // On remplit les champs du formulaire.
        $form['media[user]'] = $user->getId();
        $form['media[album]'] = $album->getId();
        $form['media[title]'] = "Un média de test";
        $form['media[file]'] = __DIR__ . '/../../Fixtures/images/test_file.jpeg';

        // On soumet le formulaire.
        $this->client->submit($form);

        // On vérifie la redirection après soumission.
        $this->assertResponseRedirects('/admin/media');

        // On suit la redirection.
        $this->client->followRedirect();

        // On récupère le média créé en base de données.
        $media = $this->entityManager->getRepository(Media::class)->findOneBy(['title' => 'Un média de test']);

        // On vérifie que le média a bien été créé en base de données.
        $this->assertNotNull($media);

        // On vérifie que le média est bien associé à l'utilisateur correct.
        $this->assertEquals($user->getId(), $media->getUser()->getId());

        // On vérifie que le média est bien associé à l'album correct.
        $this->assertEquals($album->getId(), $media->getAlbum()->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // On récupère le média en base de données.
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
