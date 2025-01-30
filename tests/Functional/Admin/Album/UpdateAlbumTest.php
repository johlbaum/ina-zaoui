<?php

namespace App\Tests\Functional\Admin\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateAlbumTest extends FunctionalTestCase
{
    public function testShouldUpdateAlbumByAdmin(): void
    {
        $this->login();

        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->find(1);
        $this->assertNotNull($album);

        $crawler = $this->get('/admin/album/update/'.$album->getId());
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form();
        $form['album[name]'] = 'Un album modifié';
        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $albumRepository = $this->entityManager->getRepository(Album::class);
        $updatedAlbum = $albumRepository->find($album->getId());

        self::assertEquals('Un album modifié', $updatedAlbum->getName());
        self::assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Un album modifié');
    }
}
