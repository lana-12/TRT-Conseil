<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\ApplyRepository;
use App\Repository\JobOfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private ApplyRepository $applyRepo,
        private JobOfferRepository $jobOfferRepo,
    )
    {
        
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        
        
        $countJobOffer = $this->jobOfferRepo->countJobOffer();
        $countApply = $this->applyRepo->countApply();
        
        return $this->render('home/index.html.twig', [
            'titlepage' => 'Page d\'accueil',
            'countApply'=> $countApply,
            'countJobOffer'=> $countJobOffer,
        ]);
    }
}