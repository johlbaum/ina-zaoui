<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private AlbumRepository $albumRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(AlbumRepository $albumRepository, EntityManagerInterface $entityManager)
    {
        $this->albumRepository = $albumRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Affiche la liste des albums.
     */
    #[Route("/admin/album", name: "admin_album_index")]
    public function index()
    {
        $albums = $this->albumRepository->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    /**
     * Ajoute un nouvel album.
     *
     * @param Request $request : la requête HTTP contenant les données du formulaire
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/admin/album/add", name: "admin_album_add")]
    public function add(Request $request)
    {
        $album = new Album();

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($album);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Met à jour un album existant.
     *
     * @param Request $request : la requête HTTP contenant les données du formulaire
     * @param int $id : l'identifiant de l'album à mettre à jour
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/admin/album/update/{id}", name: "admin_album_update")]
    public function update(Request $request, int $id)
    {
        $album = $this->albumRepository->find($id);

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * Supprime un album existant.
     *
     * @param int $id : l'identifiant de l'album à supprimer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route("/admin/album/delete/{id}", name: "admin_album_delete")]
    public function delete(int $id)
    {
        $album = $this->albumRepository->find($id);

        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}
