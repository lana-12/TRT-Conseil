<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recruiter')]

class RecruiterController extends AbstractController
{
    #[Route('/', name: 'recruiter')]
    public function index(): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
    #[Route('/profil', name: 'profilR')]
    public function profil(): Response
    {
        return $this->render('recruiter/profil.html.twig', [
            'titlepage' => 'Profil',
        ]);
    }
}