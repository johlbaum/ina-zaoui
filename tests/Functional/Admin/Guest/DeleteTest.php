<?php

namespace App\Tests\Functional\Admin\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends FunctionalTestCase
{
    public function testShouldDeleteGuestByAdmin(): void
    {
        $this->login();

        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);
        $this->assertNotNull($guest);

        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[action="/admin/guest/delete/' . $guest->getId() . '"]')->form();
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();

        $deletedGuest = $userRepository->findOneBy(['id' => $guest->getId()]);

        $this->assertNull($deletedGuest);
    }
}
