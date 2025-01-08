<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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
