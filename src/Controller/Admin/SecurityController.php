<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et gère les erreurs d'authentification.
     *
     * @param AuthenticationUtils $authenticationUtils : utilitaire pour récupérer les erreurs d'authentification
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * @throws \LogicException : cette méthode est interceptée par la configuration de sécurité
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
