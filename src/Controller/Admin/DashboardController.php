<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Course;
use App\Controller\Admin\CourseCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        
        return $this->redirect($adminUrlGenerator->setController(CourseCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('African Learners');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section();
        yield MenuItem::linkToUrl('Revenir sur le site web', 'fa fa-home', '/');
        
        yield MenuItem::section('Cours', 'fas fa-folder-open');
        yield MenuItem::linkToCrud('Liste des cours', 'fas fa-file-alt', Course::class);
        yield MenuItem::linkToCrud('Publier un cours', 'fa fa-plus', Course::class)
            ->setAction('new');
        
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Liste des utilisateurs', 'fas fa-user', User::class);
    }

}