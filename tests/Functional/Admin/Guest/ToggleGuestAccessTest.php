<?php

namespace App\Tests\Functional\Admin\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ToggleGuestAccessTest extends FunctionalTestCase
{
    public function testShouldToggleGuestAccessByAdmin(): void
    {
        $this->login();

        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);
        $this->assertNotNull($guest);

        $this->assertTrue($guest->isUserAccessEnabled());

        $crawler = $this->client->request('GET', '/admin/guest');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a.btn.btn-warning[href="/admin/guest/toggle_access/' . $guest->getId() . '"]')->link();

        $crawler = $this->client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $guest = $userRepository->find($guest->getId());
        $this->assertFalse($guest->isUserAccessEnabled());

        $crawler = $this->client->getCrawler();

        $textLink = $crawler->filter('a.btn.btn-warning[href="/admin/guest/toggle_access/' . $guest->getId() . '"]')->text();
        $this->assertEquals('Activer l\'accès', $textLink);
    }
}
