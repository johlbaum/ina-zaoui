<?php

namespace App\Tests\Functional\Home\Portfolio;

use App\Repository\AlbumRepository;
use App\Tests\Functional\FunctionalTestCase;

class ShowAMediaTest extends FunctionalTestCase
{
    public function testShouldDisplayMediasForSpecificAlbum(): void
    {
        // On récupère un album spécifique.
        $albumRepository = $this->getContainer()->get(AlbumRepository::class);
        $album = $albumRepository->findOneBy(['name' => 'Album 1']);

        $crawler = $this->get('/portfolio/' . $album->getId());
        $this->assertResponseIsSuccessful();

        // On vérifie que seuls les médias de cet album sont affichés (2 médias dans l'album 1).
        $this->assertCount(2, $crawler->filter('.media'));

        // On vérifie le titre du premier média affiché.
        $firstMediaTitle = $crawler->filter('.media')->first()->filter('.media-title')->text();
        $this->assertSame('Titre 1', $firstMediaTitle);

        // On vérifie le titre du deuxième média affiché (index 1).
        $secondMediaTitle = $crawler->filter('.media')->eq(1)->filter('.media-title')->text();
        $this->assertSame('Titre 2', $secondMediaTitle);
    }
}
