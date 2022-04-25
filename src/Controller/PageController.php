<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function contact(): Response
    {
        return $this->render('layouts/pages/contact.html.twig');
    }
}