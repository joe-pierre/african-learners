<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap',)]
    public function sitemap(Request $request, CourseRepository $courseRepo): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = ['loc' => $this->generateUrl('app_home')];
        $urls[] = ['loc' => $this->generateUrl('app_courses')];
        $urls[] = ['loc' => $this->generateUrl('app_create_course')];
        
        $urls[] = ['loc' => $this->generateUrl('app_about')];
        $urls[] = ['loc' => $this->generateUrl('app_contact')];

        $urls[] = ['loc' => $this->generateUrl('app_register')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('app_logout')];

        $urls[] = ['loc' => $this->generateUrl('app_videos')];
        $urls[] = ['loc' => $this->generateUrl('app_create_video')];        
        
        $urls[] = ['loc' => $this->generateUrl('app_account')];

        foreach ($courseRepo->findAll() as $course) {
            $urls[] = [
                'loc' => $this->generateUrl('app_show_course', [
                                'year' => $course->getCreatedAt()->format('Y'),
                                'month' => $course->getCreatedAt()->format('m'),
                                'day' => $course->getCreatedAt()->format('d'),
                                'slug' => $course->getSlug()
                            ]
                        ),
                'lastmod' => $course->getCreatedAt()->format('Y-m-d')
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls , 'hostname' => $hostname]), 200
        );

        $response->headers->set('content-type', 'text/xml');
        
        return $response;
    }
}