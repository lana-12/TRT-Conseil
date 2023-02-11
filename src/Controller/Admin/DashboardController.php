<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Apply;
use App\Entity\JobOffer;
use App\Entity\Candidate;
use App\Entity\Recruiter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TRTConeil');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Candidats', 'fas fa-user', Candidate::class);
        yield MenuItem::linkToCrud('Recruteurs', 'fas fa-user', Recruiter::class);
        yield MenuItem::linkToCrud('user', 'fas fa-user', User::class);

        yield MenuItem::section('Annonces');
        yield MenuItem::linkToCrud('Offres d\'emploi', 'fas fa-user', JobOffer::class);
        yield MenuItem::linkToCrud('Candidatures', 'fas fa-user', Apply::class);
        
    }
}
