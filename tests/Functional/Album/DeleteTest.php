<?php

namespace App\Tests\Functional\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends FunctionalTestCase
{
    public function testShouldDeleteAlbum(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On récupère l'album avec l'ID 1 depuis la base de données.
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->find(1);

        // On s'assure que l'album existe bien en base de données avant de le supprimer.
        $this->assertNotNull($album);

        // On envoie une requête DELETE pour supprimer l'album.
        $this->client->request('DELETE', '/admin/album/delete/' . $album->getId());

        // On vérifie que la réponse renvoie une redirection (code 302).
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On vérifie que l'album a bien été supprimé de la base de données.
        $deletedAlbum = $albumRepository->findOneBy(['id' => $album->getId()]);
        $this->assertNull($deletedAlbum);

        // On vérifie que l'album n'est plus affiché dans le DOM.
        $this->assertSelectorNotExists('table.table tbody tr td:contains("Album 1")');
    }
}
