<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    private MediaRepository $mediaRepository;
    private EntityManagerInterface $entityManager;
    private $fileManager;

    public function __construct(MediaRepository $mediaRepository, EntityManagerInterface $entityManager, FileManager $fileManager)
    {
        $this->mediaRepository = $mediaRepository;
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
    }

    #[Route("/admin/media", name: "admin_media_index")]
    public function index(Request $request, MediaRepository $mediaRepository)
    {
        $page = $request->query->getInt('page', 1);

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $this->mediaRepository->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );
        $total = $mediaRepository->count($criteria);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[Route("/admin/media/add", name: "admin_media_add")]
    public function add(Request $request)
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }

            $filename = md5(uniqid()) . '.' . $media->getFile()->guessExtension();

            $media->setPath($this->fileManager->getFilePath($filename));
            $media->getFile()->move($this->fileManager->getFileDirectory(), $filename);

            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/media/delete/{id}", name: "admin_media_delete")]
    public function delete(int $id)
    {
        $media = $this->mediaRepository->find($id);
        $filePath = $this->fileManager->getFilePath($media->getPath());

        $this->entityManager->remove($media);
        $this->entityManager->flush();

        unlink($filePath);

        return $this->redirectToRoute('admin_media_index');
    }
}
