<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersWithRole(string $role): array
    {
        $allUsers = $this->userRepository->findAll();

        return array_filter($allUsers, function ($user) use ($role) {
            return in_array($role, $user->getRoles(), true);
        });
    }
}
