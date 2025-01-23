<?php

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class LoginTest extends FunctionalTestCase
{
    public function testThatLoginShouldSucceeded(): void
    {
        $this->get('/login');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Connexion', [
            '_username' => 'Ina Zaoui',
            '_password' => 'password',
        ]);

        // On récupère le service AuthorizationChecker pour vérifier l'état d'authentification.
        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);

        // On vérifie que l'utilisateur est bien authentifié après la connexion.
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        // On envoie une requête GET pour se déconnecter.
        $this->get('/logout');

        // On vérifie que l'utilisateur n'est plus authentifié après la déconnexion.
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }


    public function testThatLoginShouldFailed(): void
    {
        $this->get('/login');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Connexion', [
            '_username' => 'Ina Zaoui',
            '_password' => 'password fail',
        ]);

        // On récupère le service AuthorizationChecker pour vérifier l'état d'authentification.
        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);

        // On vérifie que l'utilisateur n'est pas authentifié.
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    public function testThatLoginShouldFailForDisabledUser(): void
    {
        $this->get('/login');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Connexion', [
            '_username' => 'Invité 6', // Utilisateur dont l'accès est désactivé.
            '_password' => 'password',
        ]);

        // On récupère le service AuthorizationChecker pour vérifier l'état d'authentification.
        $authorizationChecker = $this->client->getContainer()->get(AuthorizationCheckerInterface::class);

        // On vérifie que l'utilisateur n'est pas authentifié.
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }
}
