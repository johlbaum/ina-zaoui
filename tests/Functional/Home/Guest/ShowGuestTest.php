<?php

namespace App\Tests\Functional\Home\Guest;

use App\Tests\Functional\FunctionalTestCase;
use App\Service\UserService;

class ShowGuestTest extends FunctionalTestCase
{
    public function testShouldShowGuestWithMedias(): void
    {
        $userService = $this->getContainer()->get(UserService::class);

        // On récupère le premier invité depuis la base de données dont l'accès est activé.
        $guests = $userService->findUsersWithRoleEnabled('ROLE_USER');
        $guest = $guests[1];

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
}
