<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class GuestController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Affiche la liste des invités.
     *
     * @param Request $request : la requête HTTP contenant les paramètres de pagination
     * @param PaginationService $paginationService : le service responsable de la gestion de la pagination
     * @return Response : la réponse HTTP
     */
    #[Route("/admin/guest", name: "admin_guest_index")]
    public function index(Request $request, PaginationService $paginationService): Response
    {
        $paginationParams = $paginationService->getPaginationParams($request, 25);

        // On récupère la liste de tous les invités (rôle USER).
        $guests = $this->userRepository->findPaginateGuests($paginationParams['limit'], $paginationParams['offset']);

        // On calcule le nombre total de pages nécessaires pour afficher tous les invités.
        $totalGuests = $this->userRepository->countGuests();
        $totalPages = $paginationService->getTotalPages($totalGuests, $paginationParams['limit']);

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
            'page' => $paginationParams['page'],
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Active ou désactive l'accès d'un utilisateur invité.
     *
     * @param User $guest L'utilisateur invité dont on modifie l'accès
     * @return Response : la réponse HTTP
     */
    #[Route("/admin/guest/toggle_access/{id}", name: "admin_guest_toggle_access", methods: ["GET"])]
    public function toggleGuestAccess(User $guest): Response
    {
        $guest->setUserAccessEnabled(!$guest->isUserAccessEnabled());

        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }

    /**
     * Supprime un utilisateur invité.
     *
     * @param User $guest : l'utilisateur invité à supprimer
     * @return Response La réponse HTTP
     */
    #[Route("/admin/guest/delete/{id}", name: "admin_guest_delete", methods: ["GET"])]
    public function delete(User $guest): Response
    {
        $this->entityManager->remove($guest);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }

    /**
     * Ajoute un nouvel invité.
     *
     * @param Request $request : la requête HTTP contenant les données du formulaire
     * @param UserPasswordHasherInterface $passwordHasher : le service pour hasher les mots de passe
     * @return Response : la réponse HTTP
     */
    #[Route("/admin/guest/add", name: "admin_guest_add")]
    public function add(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $guest = new User();

        $form = $this->createForm(GuestType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword($guest, $plainPassword);
            $guest->setPassword($hashedPassword);

            $guest->setRoles(['ROLE_USER']);

            $this->entityManager->persist($guest);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/add.html.twig', [
            'form' => $form,
        ]);
    }
}
