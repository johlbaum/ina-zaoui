<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class AlbumController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/admin/album", name="admin_album_index")
     */
    public function index()
    {
        $albums = $this->doctrine->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    /**
     * @Route("/admin/album/add", name="admin_album_add")
     */
    public function add(Request $request)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->persist($album);
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/album/update/{id}", name="admin_album_update")
     */
    public function update(Request $request, int $id)
    {
        $album = $this->doctrine->getRepository(Album::class)->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/album/delete/{id}", name="admin_album_delete")
     */
    public function delete(int $id)
    {
        $media = $this->doctrine->getRepository(Album::class)->find($id);
        $this->doctrine->getManager()->remove($media);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}
