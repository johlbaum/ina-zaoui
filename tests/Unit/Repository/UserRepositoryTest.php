<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends FunctionalTestCase
{
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testUpgradePassword(): void
    {
        $userService = $this->getContainer()->get(UserService::class);

        // On récupère le premier invité depuis la base de données.
        $guests = $userService->findUsersWithRoleEnabled('ROLE_USER');
        $guest = $guests[1];

        $newHashedPassword = 'newpassword';
        $this->userRepository->upgradePassword($guest, $newHashedPassword);

        // On vérifie que le mot de passe a bien été mis à jour.
        $this->assertEquals($newHashedPassword, $guest->getPassword());
    }

    public function testUpgradePasswordThrowsExceptionForNonUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        // On génère un mock d'un objet implémentant PasswordAuthenticatedUserInterface 
        // sans que ce soit une instance de la classe User.
        $mockPasswordUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // On essaye de mettre à jour le mot de passe d'un objet qui n'est pas une instance de User.
        $this->userRepository->upgradePassword($mockPasswordUser, 'newpassword');
    }
}
