<?php

namespace App\Tests\Functional\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends FunctionalTestCase
{
    public function testShouldDeleteGuest(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On récupère un invité avec l'ID 2 depuis la base de données.
        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);

        // On s'assure que l'invité existe bien en base de données avant de le supprimer.
        $this->assertNotNull($guest);

        // On envoie une requête pour pour accéder à la page de gestion des invités.
        $crawler = $this->client->request('GET', '/admin/guest');

        // On récupère le formulaire associé à l'utilisateur que l'on souhaite supprimer.
        $form = $crawler->filter('form[action="/admin/guest/delete/' . $guest->getId() . '"]')->form();

        // On soumet le formulaire.
        $this->client->submit($form);

        // On vérifie que la soumission du formulaire renvoie une redirection (code 302).
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On vérifie la suppression de l'invité de la base de données.
        $deletedGuest = $userRepository->findOneBy(['id' => $guest->getId()]);
        $this->assertNull($deletedGuest);
    }
}
