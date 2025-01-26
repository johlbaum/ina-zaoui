<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Vérifie les restrictions d'accès après l'authentification.
 *
 * @param UserInterface $user : l'utilisateur authentifié
 * @throws AuthenticationException : si l'accès utilisateur est désactivé
 */
class UserAccessChecker implements UserCheckerInterface
{
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof User && !$user->isUserAccessEnabled()) {
            throw new AuthenticationException();
        }
    }

    public function checkPreAuth(UserInterface $user): void {}
}
