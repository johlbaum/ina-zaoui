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

    public function getAllUsersWithRole(string $role): array
    {
        $allUsers = $this->userRepository->findBy([], ['id' => 'ASC']);

        return array_filter($allUsers, function ($user) use ($role) {
            return in_array($role, $user->getRoles(), true);
        });
    }

    public function getUsersWithRoleAndEnabled(string $role): array
    {
        $allUsers = $this->userRepository->findBy(['userAccessEnabled' => true], ['id' => 'ASC']);

        return array_filter($allUsers, function ($user) use ($role) {
            return in_array($role, $user->getRoles(), true);
        });
    }
}
