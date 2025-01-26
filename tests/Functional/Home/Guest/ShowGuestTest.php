<?php

namespace App\Tests\Functional\Home\Guest;

use App\Repository\UserRepository;
use App\Tests\Functional\FunctionalTestCase;

class ShowGuestTest extends FunctionalTestCase
{
    public function testShouldShowGuestWithMedias(): void
    {
        $userRepository = $this->getContainer()->get(UserRepository::class);

        $guests = $userRepository->findGuestsWithEnabledAccess();
        $guest = $guests[0];

        $crawler = $this->get('/guest/' . $guest->getId());
        $this->assertResponseIsSuccessful();

        // On vérifie que la page contient le nom de l'invité.
        $this->assertSelectorTextContains('h3', $guest->getName());

        // On vérifie que le nom de l'invité correspond bien à "Invité 1".
        $this->assertEquals('Invité 1', $guest->getName());

        // On vérifie que le nombre d'éléments affichés correspond au nombre de médias associés à cet invité.
        $mediaCount = count($guest->getMedias());
        $this->assertCount($mediaCount, $crawler->filter('.media'));

        // On vérifie que l'image du premier média de l'invité est bien affichée.
        $firstMediaSrc = $crawler->filter('.media')->first()->filter('img')->attr('src');
        $this->assertStringContainsString('uploads/0011.jpg', $firstMediaSrc);

        // On vérifie que l'image du dernier média de l'invité est bien affichée.
        $lastMediaSrc = $crawler->filter('.media')->last()->filter('img')->attr('src');
        $this->assertStringContainsString('uploads/0020.jpg', $lastMediaSrc);
    }

    public function testShouldThrowExceptionWhenGuestNotFound(): void
    {
        // Utilisation d'un ID qui ne correspond à aucun invité existant.
        $nonExistingGuestId = 9999;

        // On envoie une requête vers la page de cet invité.
        $this->get('/guest/' . $nonExistingGuestId);

        // On vérifie que le code de réponse est 404 (Page Not Found).
        $this->assertResponseStatusCodeSame(404);
    }
}
