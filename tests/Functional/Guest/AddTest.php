<?php

namespace App\Tests\Functional\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddTest extends FunctionalTestCase
{
    public function testShouldAddGuest(): void
    {
        // On connecte l'utilisateur administrateur.
        $this->login();

        // On envoie une requête pour accéder au formulaire d'ajout d'invité.
        $crawler = $this->get('/admin/guest/add');
        $this->assertResponseIsSuccessful();

        // On capture le formulaire, on remplit les champs et on soumet le formulaire.
        $form = $crawler->selectButton('Ajouter')->form();
        $form['guest[name]'] = 'Invité à ajouter';
        $form['guest[email]'] = 'invite.addtest@test.com';
        $form['guest[password]'] = 'password';
        $this->client->submit($form);

        // On vérifie que la soumission du formulaire renvoie une redirection (code 302).
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // On suit la redirection après la soumission du formulaire.
        $this->client->followRedirect();

        // On récupère le guest créé en base de données.
        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->findOneBy(['email' => 'invite.addtest@test.com']);

        // On vérifie que l'invité existe bien dans la base de données, on vérifie son nom, son email et qu'il s'affiche bien dans le DOM.
        $this->assertNotNull($guest);
        $this->assertEquals('Invité à ajouter', $guest->getName());
        $this->assertEquals('invite.addtest@test.com', $guest->getEmail());
        $this->assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Invité à ajouter');
    }
}
