<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Repository\ApplyRepository;
use App\Repository\CandidateRepository;
use App\Repository\JobOfferRepository;
use App\Repository\RecruiterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private ApplyRepository $applyRepo,
        private JobOfferRepository $jobOfferRepo,
        private RecruiterRepository $recruiterRepo,
        private CandidateRepository $candidateRepo,
    )
    {
        
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user) {
            $recruiter= $user->getRecruiters();
            
            $name = $this->recruiterRepo->findOneByName($recruiter);
            dump($name);
        } 
        
        $countJobOffer = $this->jobOfferRepo->countJobOffer();
        $countApply = $this->applyRepo->countApply();

        
        return $this->render('home/index.html.twig', [
            'titlepage' => 'Page d\'accueil',
            'countApplies'=> $countApply,
            'countJobOffers'=> $countJobOffer,
            'recruiters'=>$recruiter,
        ]);
    }
}