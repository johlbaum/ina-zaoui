<?php

namespace App\Tests\Functional\Home\Guest;

use App\Tests\Functional\FunctionalTestCase;
use App\Service\UserService;

class ShowGuestsTest extends FunctionalTestCase
{
    public function testShouldShowAllGuests(): void
    {
        $userService = $this->getContainer()->get(UserService::class);

        // On récupèrer les invités avec le rôle 'ROLE_USER' et qui sont activés.
        $guests = $userService->findUsersWithRoleEnabled('ROLE_USER');

        $crawler = $this->get('/guests');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h3', 'Invités');

        // On vérifie que le nombre d'invités dans le DOM correspond au nombre d'invités récupérés par le service.
        $this->assertCount(count($guests), $crawler->filter('.guest'));

        // On vérifie que le premier invité affiché est "Invité 1" avec 10 médias associés.
        $firstGuestText = $crawler->filter('.guest h4')->first()->text();
        $this->assertStringContainsString('Invité 1 (10)', $firstGuestText);

        // On vérifie que le dernier invité affiché est "Invité 10" avec 10 médias associés.
        $lastGuestText = $crawler->filter('.guest h4')->last()->text();
        $this->assertStringContainsString('Invité 5 (10)', $lastGuestText);
    }

    public function testShouldNotShowPhotosOfRevokedGuest(): void
    {
        $userService = $this->getContainer()->get(UserService::class);

        // On sélectionne un invité dont l'accès est désactivé.
        $guests = $userService->findUsersWithRoleDisabled('ROLE_USER');
        $revokedGuest = $guests[1];

        $crawler = $this->get('/guests');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h3', 'Invités');

        // On vérifie que l'invité dont l'accès est révoqué n'est pas présent sur la page.
        $this->assertStringNotContainsString($revokedGuest->getName(), $crawler->text());
    }
}
