<?php

namespace App\Tests\Functional\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddTest extends FunctionalTestCase
{
    public function testShouldAddAlbum(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On envoie une requête pour accéder à la page d'ajout d'album.
        $crawler = $this->get('/admin/album/add');
        $this->assertResponseIsSuccessful();

        // On capture le bouton de soumission du formulaire, on remplit les champs et on soumet le formulaire.
        $form = $crawler->selectButton('Ajouter')->form();
        $form['album[name]'] = 'Album test';
        $this->client->submit($form);

        // On vérifie que la soumission du formulaire renvoie une redirection (code 302).
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On récupère l'album créé en base de données.
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->findOneBy(['name' => 'Album test']);

        // On vérifie que l'album existe bien dans la base de données, on vérifie son nom et qu'il s'affiche bien dans le DOM.
        self::assertNotNull($album);
        self::assertEquals('Album test', $album->getName());
        self::assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Album test');
    }
}
