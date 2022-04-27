<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/les-videos")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/", name="app_videos", methods={"GET"})
     */
    public function all_videos(VideoRepository $videoRepository): Response
    {
        return $this->render('layouts/videos/videos.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/publier-une-video", name="app_create_video", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'Votre compte n\'est pas activé, veuillez activer votre compte pour acccéder à plus de fonctionnalités');

            return $this->redirectToRoute('app_home');
        }

        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setUser($this->getUser());
            $em->persist($video);
            $em->flush();
            
            $this->addFlash('success', 'La vidéo a été publiée');

            return $this->redirectToRoute('app_videos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('layouts/videos/create.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}", name="app_show_video", methods={"GET"})
     */
    public function show(Video $video): Response
    {
        return $this->render('layouts/videos/show.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}/modifier-video", name="app_edit_video", methods={"GET", "POST"})
     */
    public function edit(Request $request, Video $video, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_home');
        }

        if ($video->getUser() != $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas le droit d\'effectuer cette action');

            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('info', 'Le cours a été mise à jour');

            return $this->redirectToRoute('app_videos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('layouts/videos/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}/supprimer", name="app_delete_video", methods={"POST"})
     */
    public function delete(Request $request, Video $video, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $em->remove($video);
            $em->flush();
        }

        return $this->redirectToRoute('app_videos', [], Response::HTTP_SEE_OTHER);
    }
}