<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    private UserRepository $userRepository;
    private AlbumRepository $albumRepository;
    private MediaRepository $mediaRepository;
    private PaginationService $paginationService;

    public function __construct(
        UserRepository $userRepository,
        AlbumRepository $albumRepository,
        MediaRepository $mediaRepository,
        PaginationService $paginationService
    ) {
        $this->userRepository = $userRepository;
        $this->albumRepository = $albumRepository;
        $this->mediaRepository = $mediaRepository;
        $this->paginationService = $paginationService;
    }

    /**
     * Affiche la page d'accueil.
     */
    #[Route("/", name: "home")]
    public function home()
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * Affiche la liste des invités.
     *
     * @param Request $request : la requête HTTP contenant les paramètres de pagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/guests", name: "guests")]
    public function guests(Request $request)
    {
        $paginationParams = $this->paginationService->getPaginationParams($request, 25);

        // On récupère le nom et le nombre total de médias associés de tous les invités (rôles USER) 
        // dont l'accès est activé par tranche de 25.
        $guests = $this->userRepository->findPaginateGuestsWithMediaCount($paginationParams['limit'], $paginationParams['offset']);

        // On calcule le nombre total de pages nécessaires pour afficher tous les invités.
        $totalGuests = $this->userRepository->countGuestsWithAccessEnabled();
        $totalPages = $this->paginationService->getTotalPages($totalGuests, $paginationParams['limit']);

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
            'page' => $paginationParams['page'],
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Affiche les détails d'un invité spécifique.
     *
     * @param Request $request : la requête HTTP contenant les paramètres de pagination
     * @param int $id : l'identifiant de l'invité
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/guest/{id}", name: "guest")]
    public function guest(Request $request, int $id)
    {
        $paginationParams = $this->paginationService->getPaginationParams($request, 12);

        // On récupère l'invité par son ID parmi la liste des invités (rôle USER) dont l'accès est activé.
        $guest = $this->userRepository->find($id);
        if (!$guest) {
            throw $this->createNotFoundException("L'invité " . $id . " n'existe pas.");
        }

        // On récupère les médias associés à l'invité par tranche de 12.
        $mediaList = $this->mediaRepository->findPaginateMediaList($paginationParams['limit'], $paginationParams['offset'], null, $guest);

        // On calcule le nombre total de pages nécessaires pour afficher les médias de l'invité.
        $totalMedia = $this->mediaRepository->countMedia(null, $guest);
        $totalPages = $this->paginationService->getTotalPages($totalMedia, $paginationParams['limit']);

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
            'mediaList' => $mediaList,
            'page' => $paginationParams['page'],
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Affiche le portfolio, avec ou sans un album spécifique.
     *
     * @param Request $request : la requête HTTP contenant les paramètres de pagination
     * @param int|null $id : l'identifiant de l'album 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/portfolio/{id?}", name: "portfolio")]
    public function portfolio(Request $request, ?int $id = null)
    {
        $paginationParams = $this->paginationService->getPaginationParams($request, 12);

        // Si un ID d'album est fourni, on récupère l'album correspondant. Sinon, on récupère tous les albums.
        $albums = $this->albumRepository->findAll();
        $album = $id ? $this->albumRepository->find($id) : null;
        if ($id && !$album) {
            throw $this->createNotFoundException("L'album avec l'ID " . $id . " n'existe pas.");
        }

        // On récupère l'administrateur (rôle ADMIN).
        $admin = $this->userRepository->findAdminUser();

        // On récupère les médias associés à l'administrateur par tranche de 12.
        $mediaList = $this->mediaRepository->findPaginateMediaList($paginationParams['limit'], $paginationParams['offset'], $album, $admin);

        // On calcule le nombre total de pages nécessaires pour afficher les médias de l'administrateur.
        $totalMedia = $this->mediaRepository->countMedia($album, $admin);
        $totalPages = $this->paginationService->getTotalPages($totalMedia, $paginationParams['limit']);

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'mediaList' => $mediaList,
            'totalPages' => $totalPages,
            'page' => $paginationParams['page']
        ]);
    }

    /**
     * Affiche la page "À propos".
     */
    #[Route("/about", name: "about")]
    public function about()
    {
        return $this->render('front/about.html.twig');
    }

    /**
     * Affiche la page d'administration.
     */
    #[Route("/admin", name: "admin")]
    public function admin()
    {
        return $this->render('admin.html.twig');
    }
}
