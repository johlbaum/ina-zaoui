<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
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
        // On initialise un tableau pour stocker les médias communs aux invités.
        $mediaArray = [];

        // On crée 100 utilisateurs invités avec des médias associés.
        for ($i = 1; $i <= 100; $i++) {
            $userGuest = new User();
            $userGuest->setName('Invité ' . $i);
            $userGuest->setEmail('invite' . $i . '@example.com');
            $userGuest->setRoles(["ROLE_USER"]);
            $userGuest->setPassword($this->userPasswordHasher->hashPassword($userGuest, "password"));
            $userGuest->setDescription('Description personnalisée...');
            $userGuest->setUserAccessEnabled(true);

            $manager->persist($userGuest);

            // On génère et sauvegarde 30 médias lors de la première itération.
            if ($i === 1) {
                for ($j = 1; $j <= 30; $j++) {
                    $media = new Media();
                    $media->setTitle('Titre ' . $j);
                    $media->setPath('uploads/' . str_pad($j, 3, '0', STR_PAD_LEFT) . '.jpg'); // ex. : 'uploads/001.jpg'
                    $media->setUser($userGuest);
                    $media->setAlbum(null);

                    // On persiste chaque média et l'ajoute au tableau de médias communs.
                    $manager->persist($media);
                    $mediaArray[] = $media;
                }
            } else {
                // On associe les médias déjà générés aux autres invités.
                foreach ($mediaArray as $mediaTemplate) {
                    $media = new Media();
                    $media->setTitle($mediaTemplate->getTitle());
                    $media->setPath($mediaTemplate->getPath());
                    $media->setUser($userGuest);
                    $media->setAlbum(null);

                    $manager->persist($media);
                }
            }
        }

        // On crée un utilisateur administrateur.
        $userAdmin = new User();
        $userAdmin->setName('Ina Zaoui');
        $userAdmin->setEmail("ina@zaoui.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $userAdmin->setDescription(null);

        $manager->persist($userAdmin);

        // On initialise un tableau pour stocker les albums.
        $albums = [];

        // On crée 5 albums pour l'administrateur.
        for ($i = 1; $i <= 5; $i++) {
            $album = new Album();
            $album->setName('Album ' . $i);

            // On persiste chaque album et l'ajoute au tableau.
            $manager->persist($album);
            $albums[] = $album;
        }

        // On génère 150 médias pour l'administrateur (30 par album).
        for ($i = 1; $i <= 150; $i++) {
            $media = new Media();
            $media->setTitle('Titre ' . $i);
            $media->setPath('uploads/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg');
            $media->setUser($userAdmin);

            // On attribue les médias à des albums, 30 par album.
            $albumIndex = intdiv($i - 1, 30);
            $media->setAlbum($albums[$albumIndex]);

            $manager->persist($media);
        }

        $manager->flush();
    }
}
