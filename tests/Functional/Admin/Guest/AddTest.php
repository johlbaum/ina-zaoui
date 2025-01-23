<?php

namespace App\Tests\Functional\Admin\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddTest extends FunctionalTestCase
{
    public function testShouldAddGuestByAdmin(): void
    {
        $this->login();

        $crawler = $this->get('/admin/guest/add');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form();
        $form['guest[name]'] = 'Invité à ajouter';
        $form['guest[email]'] = 'invite.addtest@test.com';
        $form['guest[password]'] = 'password';
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->findOneBy(['email' => 'invite.addtest@test.com']);

        $this->assertNotNull($guest);
        $this->assertEquals('Invité à ajouter', $guest->getName());
        $this->assertEquals('invite.addtest@test.com', $guest->getEmail());
        $this->assertSelectorTextContains('table.table tbody tr:last-child td:first-child', 'Invité à ajouter');
    }
}
