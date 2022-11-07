<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/consultant')]

class ConsultantController extends AbstractController
{
    #[Route('/', name: 'consultant')]
    public function index(): Response
    {
        return $this->render('consultant/index.html.twig', [
            'titlepage' => 'Consultant',
        ]);
    }
}