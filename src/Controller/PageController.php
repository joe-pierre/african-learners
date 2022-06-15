<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    /**
     * @Route("/a-propos", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('layouts/pages/about.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createFormBuilder()
                    ->add('name', TextType::class, [
                        'label' => 'Prénom(s) et Nom',
                        'constraints' => [
                            new NotBlank,
                        ]
                    ])
                    ->add('email', EmailType::class, [
                        'label' => 'Email',
                        'constraints' => [
                            new NotBlank,
                        ]
                    ])
                    ->add('message', TextareaType::class, ['label' => 'Message',])
                    ->getForm();

        $form->handleRequest($request);
        
        $alearnersEmail = $this->getParameter('app.alearners_email');

        if ($form->isSubmitted() && $form->isValid()) {
            $sender = $form->get("email")->getViewData();
            $message = $form->get("message")->getViewData();

            $email = (new Email())
                ->from($alearnersEmail)
                ->to($alearnersEmail)
                ->subject('Message de '. $sender . ' envoyé depuis a-learners.com')
                ->text($message);

            $mailer->send($email);
            
            $this->addFlash('info', 'Message envoyé! ✅');

            return $this->redirectToRoute('app_contact');
        }

        return $this->renderForm('layouts/pages/contact.html.twig', compact('form'));
    }
}