<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * @Route("/compte")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     */
    public function show(CourseRepository $course): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        
        // Get current User Id
        $user_id = $this->getUser()->getId();
        // dd($user_id);

        $user_courses = $course->findAllCoursesFromSpecificUser('user', $user_id);

        // dd($user_courses);

        // foreach ($user_courses as $course) {
        //     dump($course);
        // }

        // die;

        return $this->render('layouts/account/user_profile_page.html.twig', [
            'user_courses' => $user_courses,
        ]);
    }

    /**
     * @Route("/mettre-a-jour-mon-compte", name="app_account_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('layouts/account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier-mon-mot-de-passe", name="app_account_change_password", methods="GET|POST")
     */
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $this->addFlash('success', 'Votre mot de passe a été mis à jour');

            $em->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('layouts/account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}