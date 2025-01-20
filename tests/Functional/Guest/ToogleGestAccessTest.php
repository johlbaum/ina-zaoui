<?php

namespace App\Tests\Functional\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class ToggleAccessTest extends FunctionalTestCase
{
    public function testShouldToggleGuestAccess(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On récupère un invité avec l'ID 2 depuis la base de données.
        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);

        // On vérifie que l'utilisateur existe bien dans la base de données.
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->assertNotNull($userRepository->find($guest->getId()));

        // On vérifie que l'accès est activé au départ.
        $this->assertTrue($guest->isUserAccessEnabled());

        // On envoie une requête.
        $crawler = $this->client->request('GET', '/admin/guest');

        // On récupère le formulaire associé à l'utilisateur.
        $form = $crawler->filter('form[action="/admin/guest/toggle_access/' . $guest->getId() . '"]')->form();

        // On soumet le formulaire.
        $this->client->submit($form);

        // On vérifie que la soumission du formulaire renvoie une redirection (code 302).
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On vérifie que l'accès a bien été modifié dans la base de données.
        $guest = $userRepository->find($guest->getId());
        $this->assertFalse($guest->isUserAccessEnabled());

        // On récupère à nouveau le crawler après la redirection.
        $crawler = $this->client->getCrawler();

        // On vérifie que le texte du bouton a bien changé.
        $textButton = $crawler->filter('form[action="/admin/guest/toggle_access/' . $guest->getId() . '"] button')->text();
        $this->assertEquals('Activer l\'accès', $textButton);
    }
}
