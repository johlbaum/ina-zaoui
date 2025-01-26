<?php

namespace App\Tests\Functional\Admin\Guest;

use App\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteGuestTest extends FunctionalTestCase
{
    public function testShouldDeleteGuestByAdmin(): void
    {
        $this->login();

        $userRepository = $this->entityManager->getRepository(User::class);
        $guest = $userRepository->find(2);
        $this->assertNotNull($guest);

        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a.btn.btn-danger[href="/admin/guest/delete/' . $guest->getId() . '"]')->link();

        $crawler = $this->client->click($link);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();

        $deletedGuest = $userRepository->findOneBy(['id' => $guest->getId()]);
        $this->assertNull($deletedGuest);
    }
}
