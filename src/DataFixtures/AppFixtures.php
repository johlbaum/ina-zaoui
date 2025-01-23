<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public static function getGroups(): array
    {
        return ['production'];
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'administrateur.
        $userAdmin = new User();

        $userAdmin->setName('Ina Zaoui');
        $userAdmin->setEmail("ina@zaoui.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $userAdmin->setDescription(null);

        $manager->persist($userAdmin);

        // Création de 100 invités.
        for ($i = 1; $i <= 100; $i++) {

            $userGuest = new User();

            $userGuest->setName('Invité ' . $i);
            $userGuest->setEmail('invite' . $i . '@example.com');
            $userGuest->setRoles(["ROLE_USER"]);
            $userGuest->setPassword($this->userPasswordHasher->hashPassword($userGuest, "password"));
            $userGuest->setDescription('Le maître de l\'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l\'énergie des rues dans des instants éblouissants. À travers une technique avant-gardiste, il métamorphose le béton et l\'acier en toiles abstraites.');

            $manager->persist($userGuest);
        }

        $manager->flush();
    }
}
