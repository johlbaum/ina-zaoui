<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use App\Form\GuestAccessType;
use App\Form\GuestDeleteType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class GuestController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/admin/guest", name: "admin_guest_index")]
    public function index(UserRepository $userRepository)
    {
        $guests = $userRepository->findGuests();

        $formsAccess = [];
        $formsDelete = [];

        // Pour chaque invité, on génère deux formulaires :
        // - Un formulaire pour activer ou désactiver l'accès à l'espace invité.
        // - Un formulaire pour supprimer le compte utilisateur.
        foreach ($guests as $guest) {
            $formsAccess[$guest->getId()] = $this->createForm(GuestAccessType::class, null, [
                'is_enabled' => $guest->isUserAccessEnabled(),
            ])->createView();

            $formsDelete[$guest->getId()] = $this->createForm(GuestDeleteType::class, null)->createView();
        }

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
            'formsAccess' => $formsAccess,
            'formsDelete' => $formsDelete
        ]);
    }

    #[Route("/admin/guest/toggle_access/{id}", name: "admin_guest_toggle_access", methods: ["POST"])]
    public function toggleGuestAccess(Request $request, User $guest): Response
    {
        $form = $this->createForm(GuestAccessType::class, null, [
            'is_enabled' => $guest->isUserAccessEnabled(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $guest->setUserAccessEnabled(!$guest->isUserAccessEnabled());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }
    }

    #[Route("/admin/guest/delete/{id}", name: "admin_guest_delete", methods: ["POST"])]
    public function delete(Request $request, User $guest): Response
    {
        $form = $this->createForm(GuestDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->entityManager->remove($guest);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }
    }


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
