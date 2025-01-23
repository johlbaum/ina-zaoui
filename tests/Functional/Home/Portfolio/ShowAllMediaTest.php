<?php

namespace App\Tests\Functional\Home\Portfolio;

use App\Tests\Functional\FunctionalTestCase;

class ShowAllMediaTest extends FunctionalTestCase
{
    public function testShouldDisplayAllMedias(): void
    {
        $crawler = $this->get('/portfolio');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h3', 'Portfolio');

        // On vérifie que tous les albums sont affichés (5 albums dans les fixtures).
        $this->assertCount(5, $crawler->filter('.btn:not(.active)'));

        // On vérifie que tous les médias de l'administrateur sont affichés (10 médias, 2 par album).
        $this->assertCount(10, $crawler->filter('.media'));

        // On vérifie le titre du premier média affiché.
        $firstMediaTitle = $crawler->filter('.media')->first()->filter('.media-title')->text();
        $this->assertSame('Titre 1', $firstMediaTitle);

        // On vérifie le titre du dernier média affiché.
        $lastMediaTitle = $crawler->filter('.media')->last()->filter('.media-title')->text();
        $this->assertSame('Titre 10', $lastMediaTitle);
    }
}
