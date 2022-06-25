<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use Vich\UploaderBundle\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class CourseController extends AbstractController
{
    private $courseRepository;
    private $slugger;

    public function __construct(CourseRepository $courseRepository, SluggerInterface $slugger)
    {
        $this->courseRepository = $courseRepository;
        $this->slugger = $slugger;
    }

    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->render('layouts/courses/home.html.twig', [
            'courses' => $this->courseRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    /**
     * @Route("/les-cours", name="app_courses", methods={"GET"})
     */
    public function all_courses(PaginatorInterface $paginator, Request $request): Response
    {
        $data = $this->courseRepository->findAll();

        $courses = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            9,
            [
                'defaultSortFieldName' => 'createdAt',
                'defaultSortDirection' => 'DESC',
            ]
        );

        return $this->render('layouts/courses/courses.html.twig', [
            'courses' => $courses
        ]);
    }

    /**
     * @Route("/publier-un-cours", name="app_create_course", methods={"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo, UploaderHelper $helper): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'Votre compte n\'est pas encore activé, veuillez consulter votre boite mail ou contacter l\'administrateur.');

            return $this->redirectToRoute('app_home');
        }

        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($course->getTitle());

            $slug = u($slug)->lower();
            
            $course->setUser($this->getUser());
            $course->setSlug($slug);
            
            $em->persist($course);
            $em->flush();

            $this->addFlash('success', 'Le cours a été publié');

            return $this->redirectToRoute('app_courses', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('layouts/courses/create.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/cours/{year}/{month}/{day}/{slug}", name="app_show_course", methods={"GET"})
     */
    public function show(int $year, int $month, int $day, string $slug): Response
    {
        $course = $this->courseRepository->findOneByPublishedDateAndSlug($year, $month, $day, $slug);

        return $this->render('layouts/courses/show.html.twig', compact('course'));
    }

    /**
     * @Route("/cours/{id<[0-9]+>}/modifier", name="app_edit_course", methods={"GET","POST"})
     */
    public function edit(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_home');
        }

        if ($course->getUser() != $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas le droit d\'effectuer cette action');

            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('info', 'Le cours a été modifié');

            return $this->redirectToRoute('app_courses', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('layouts/courses/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}/supprimer", name="app_delete_course", methods={"POST"})
     */
    public function delete(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_home');
        }

        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'Votre compte n\'est pas activé, veuillez vérifier votre boîte mail');

            return $this->redirectToRoute('app_home');
        }

        if ($course->getUser() != $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas le droit d\'effectuer cette action');

            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $em->remove($course);
            $em->flush();

            $this->addFlash('warning', 'Le cours a été supprimé');
        }

        return $this->redirectToRoute('app_courses', [], Response::HTTP_SEE_OTHER);
    }
}