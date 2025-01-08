<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

class HomeController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route("/", name: "home")]
    public function home()
    {
        return $this->render('front/home.html.twig');
    }

    #[Route("/guests", name: "guests")]
    public function guests()
    {
        $guests = $this->userService->getUsersWithRoleAndEnabled('ROLE_USER');

        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route("/guest/{id}", name: "guest")]
    public function guest(UserRepository $userRepository, int $id)
    {
        $guest = $userRepository->find($id);

        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route("/portfolio/{id?}", name: "portfolio")]
    public function portfolio(AlbumRepository $albumRepository, MediaRepository $mediaRepository, ?int $id = null)
    {
        $albums = $albumRepository->findAll();

        $album = $id ? $albumRepository->find($id) : null;

        $admin = $this->userService->getAllUsersWithRole('ROLE_ADMIN');

        $medias = $album
            ? $mediaRepository->findBy(['album' => $album])
            : $mediaRepository->findBy(['user' => $admin]);

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    #[Route("/about", name: "about")]
    public function about()
    {
        return $this->render('front/about.html.twig');
    }

    #[Route("/admin", name: "admin")]
    public function admin()
    {
        return $this->render('admin.html.twig');
    }
}
