<?php

namespace App\Tests\Functional\Admin\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddTest extends FunctionalTestCase
{
    public function testShouldAddAlbumByAdmin(): void
    {
        $this->login();

        $crawler = $this->get('/admin/album/add');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form();
        $form['album[name]'] = 'Album test';
        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->findOneBy(['name' => 'Album test']);

        self::assertNotNull($album);
        self::assertEquals('Album test', $album->getName());
        self::assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Album test');
    }
}
