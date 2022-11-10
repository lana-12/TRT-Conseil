<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobOffer')]

class JobOfferController extends AbstractController
{
    #[Route('/', name: 'jobOffer')]
    public function index(): Response
    {
        return $this->render('jobOffer/index.html.twig', [
            'titlepage' => 'Offres d\'emploi',
        ]);
    }
}