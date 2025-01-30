<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Service\FileManager;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    private MediaRepository $mediaRepository;
    private EntityManagerInterface $entityManager;
    private $fileManager;

    public function __construct(
        MediaRepository $mediaRepository,
        EntityManagerInterface $entityManager,
        FileManager $fileManager,
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
    }

    /**
     * Affiche la liste des médias dans l'espace administrateur.
     *
     * @param Request           $request           : la requête HTTP contenant les paramètres de pagination
     * @param PaginationService $paginationService : le service de gestion de la pagination
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/admin/media', name: 'admin_media_index')]
    public function index(Request $request, PaginationService $paginationService)
    {
        $paginationParams = $paginationService->getPaginationParams($request, 25);

        // Si l'utilisateur n'a pas le rôle ADMIN, on ajoute un critère pour filtrer les médias par utilisateur courant.
        $criteria = [];
        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        // Si l'utilisateur a le rôle ADMIN, on récupère tous les médias (les siens et ceux des invités) par tranche de 25.
        // Si l'utilisateur a le rôle USER (il s'agit d'un invité), on récupère uniquement ses propres médias par tranche de 25.
        $mediaList = $this->mediaRepository->findPaginateMediaList($paginationParams['limit'], $paginationParams['offset'], null, $criteria['user'] ?? null);

        // On calcule le nombre total de pages nécessaires pour afficher les médias (tous les médias ou uniquement ceux de l'invité).
        $totalMedia = $this->mediaRepository->countMedia(null, $criteria['user'] ?? null);
        $totalPages = $paginationService->getTotalPages($totalMedia, $paginationParams['limit']);

        return $this->render('admin/media/index.html.twig', [
            'mediaList' => $mediaList,
            'page' => $paginationParams['page'],
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Ajoute un nouveau média.
     *
     * @param Request $request : la requête HTTP contenant les données du formulaire
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/admin/media/add', name: 'admin_media_add')]
    public function add(Request $request)
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }

            // On génère un nom unique pour le fichier image en utilisant un hash.
            $filename = md5(uniqid()).'.'.$media->getFile()->guessExtension();

            // On définit le chemin complet du fichier image, en fonction de l'environnement (prod ou test).
            $media->setPath($this->fileManager->getFilePath($filename));

            // On déplace le fichier téléchargé vers son répertoire de destination, en fonction de l'environnement (prod ou test).
            $media->getFile()->move($this->fileManager->getFileDirectory(), $filename);

            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Supprime un média et son fichier associé.
     *
     * @param int $id : l'identifiant du média à supprimer
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/admin/media/delete/{id}', name: 'admin_media_delete')]
    public function delete(int $id)
    {
        $media = $this->mediaRepository->find($id);
        if (!$media) {
            throw $this->createNotFoundException("Le média avec l'ID ".$id." n'existe pas.");
        }

        // On récupère le chemin complet du fichier image en fonction de l'environnement (prod ou test).
        $filePath = $this->fileManager->getFilePath($media->getPath());

        // On supprime le média de la base de données.
        $this->entityManager->remove($media);
        $this->entityManager->flush();

        // On supprime physiquement le fichier image.
        unlink($filePath);

        return $this->redirectToRoute('admin_media_index');
    }
}
