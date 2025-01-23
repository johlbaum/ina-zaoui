<?php

namespace App\Tests\Functional\Admin\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ToggleAccessTest extends FunctionalTestCase
{
    public function testShouldToggleGuestAccessByAdmin(): void
    {
        $this->login();

        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);
        $this->assertNotNull($userRepository->find($guest->getId()));

        // On vérifie que l'accès est activé au départ.
        $this->assertTrue($guest->isUserAccessEnabled());

        $crawler = $this->client->request('GET', '/admin/guest');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[action="/admin/guest/toggle_access/' . $guest->getId() . '"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $guest = $userRepository->find($guest->getId());
        $this->assertFalse($guest->isUserAccessEnabled());

        // On récupère à nouveau le crawler après la redirection.
        $crawler = $this->client->getCrawler();

        // On vérifie que le texte du bouton a bien changé.
        $textButton = $crawler->filter('form[action="/admin/guest/toggle_access/' . $guest->getId() . '"] button')->text();
        $this->assertEquals('Activer l\'accès', $textButton);
    }
}
