<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GuestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class GuestController extends AbstractController
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;

    public function __construct(UserService $userService, EntityManagerInterface $entityManager)
    {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    #[Route("/admin/guest", name: "admin_guest_index")]
    public function index()
    {
        $guests = $this->userService->getUsersWithRole('ROLE_USER');

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests
        ]);
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
            'form' => $form->createView(),
        ]);
    }
}
