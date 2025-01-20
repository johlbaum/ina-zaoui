<?php

namespace App\Tests\Functional\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends FunctionalTestCase
{
    public function testShouldUpdateAlbum(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On récupère l'album avec l'ID 1 depuis la base de données.
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->find(1);

        // On s'assure que l'album est bien en base de données avant de le mettre à jour.
        $this->assertNotNull($album);

        // On envoie une requête pour accéder à la page d'édition de l'album.
        $crawler = $this->get('/admin/album/update/' . $album->getId());

        // On vérifie que la page d'édition s'affiche correctement.
        $this->assertResponseIsSuccessful();

        // On capture le bouton de soumission du formulaire et on remplit les champs avec de nouvelles données.
        $form = $crawler->selectButton('Modifier')->form();
        $form['album[name]'] = 'Un album modifié';

        // On soumet le formulaire.
        $this->client->submit($form);

        // On vérifie que la soumission du formulaire renvoie une redirection (code 302).
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On récupère l'album modifié depuis la base de données.
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $updatedAlbum = $albumRepository->find($album->getId());

        // On vérifie que le nom de l'album a bien été mis à jour.
        self::assertEquals('Un album modifié', $updatedAlbum->getName());

        // On vérifie que le dernier élément ajouté dans le tableau correspond au nom de l'album modifié.
        self::assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Un album modifié');
    }
}
