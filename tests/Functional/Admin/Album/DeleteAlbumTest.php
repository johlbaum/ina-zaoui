<?php

namespace App\Tests\Functional\Admin\Album;

use App\Entity\Album;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteAlbumTest extends FunctionalTestCase
{
    public function testShouldDeleteAlbumByAdmin(): void
    {
        $this->login();

        $albumRepository = $this->entityManager->getRepository(Album::class);
        $album = $albumRepository->find(1);
        $this->assertNotNull($album);

        $this->delete('/admin/album/delete/'.$album->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $deletedAlbum = $albumRepository->findOneBy(['id' => $album->getId()]);

        $this->assertNull($deletedAlbum);
        $this->assertSelectorNotExists('table.table tbody tr td:contains("Album 1")');
    }
}
