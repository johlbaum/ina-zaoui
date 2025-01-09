<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AppFixturesTests extends Fixture implements FixtureGroupInterface
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public static function getGroups(): array
    {
        return ['tests'];
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

        // Création de 5 albums.
        $albums = [];
        for ($i = 1; $i <= 5; $i++) {
            $album = new Album();
            $album->setName('Album ' . $i);

            $manager->persist($album);
            $albums[] = $album;
        }

        // Création de 10 médias pour l'administrateur (2 médias par album).
        $mediaCount = 1;
        foreach ($albums as $album) {
            for ($i = 1; $i <= 2; $i++) {
                $media = new Media();
                $media->setTitle('Titre ' . $mediaCount);
                $media->setPath('uploads/' . str_pad($mediaCount, 4, '0', STR_PAD_LEFT) . '.jpg'); // ex. : 'uploads/0001.jpg'
                $media->setUser($userAdmin);
                $media->setAlbum($album);
                $manager->persist($media);
                $mediaCount++;
            }
        }

        // Création de 5 invités à l'accès activé.
        for ($i = 1; $i <= 5; $i++) {
            $userGuest = new User();
            $userGuest->setName('Invité ' . $i);
            $userGuest->setEmail('invite' . $i . '@example.com');
            $userGuest->setRoles(["ROLE_USER"]);
            $userGuest->setPassword($this->userPasswordHasher->hashPassword($userGuest, "password"));
            $userGuest->setDescription('Le maître de l\'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l\'énergie des rues dans des instants éblouissants. À travers une technique avant-gardiste, il métamorphose le béton et l\'acier en toiles abstraites.');
            $userGuest->setUserAccessEnabled(true);

            $manager->persist($userGuest);

            // Création de 10 médias pour chaque invité.
            $mediaCount = ($i - 1) * 10 + 11; // On a déjà créé 10 médias pour l'administrateur. On souhaite reprendre le compte à 11. Le premier invité ($i = 1) aura ses médias numérotés de 11 à 20, le deuxième invité ($i = 2) aura ses médias numérotés de 21 à 30 etc.
            for ($j = 1; $j <= 10; $j++) {
                $media = new Media();
                $media->setTitle('Titre ' . $mediaCount);
                $media->setPath('uploads/' . str_pad($mediaCount, 4, '0', STR_PAD_LEFT) . '.jpg');
                $media->setUser($userGuest);
                $media->setAlbum(null); // Aucun album assigné pour les invités.

                $manager->persist($media);
                $mediaCount++;
            }
        }

        // Création de 5 invités à l'accès désactivé.
        for ($i = 6; $i <= 10; $i++) { // On a déjà créé 5 invités. On souhaite reprendre les identifiants à 6.
            $userGuest = new User();
            $userGuest->setName('Invité ' . $i);
            $userGuest->setEmail('invite' . $i . '@example.com');
            $userGuest->setRoles(["ROLE_USER"]);
            $userGuest->setPassword($this->userPasswordHasher->hashPassword($userGuest, "password"));
            $userGuest->setDescription('Le maître de l\'urbanité capturée, explore les méandres des cités avec un regard vif et impétueux, figeant l\'énergie des rues dans des instants éblouissants. À travers une technique avant-gardiste, il métamorphose le béton et l\'acier en toiles abstraites.');
            $userGuest->setUserAccessEnabled(false);

            $manager->persist($userGuest);

            // Création de 10 médias pour chaque invité.
            $mediaCount = ($i - 1) * 10 + 11;
            for ($j = 1; $j <= 10; $j++) { // On a déjà créé 10 médias pour l'administrateur et 5 * 10 médias pour les invités à l'accès activé. On souhaite reprendre à 61 les identifiants.
                $media = new Media();
                $media->setTitle('Titre ' . $mediaCount);
                $media->setPath('uploads/' . str_pad($mediaCount, 4, '0', STR_PAD_LEFT) . '.jpg');
                $media->setUser($userGuest);
                $media->setAlbum(null);

                $manager->persist($media);
                $mediaCount++;
            }
        }

        $manager->flush();
    }
}
