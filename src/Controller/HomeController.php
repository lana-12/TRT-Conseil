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
    public function home(): Response
    {
        $countJobOffer = $this->jobOfferRepo->countJobOffer();
        $countApply = $this->applyRepo->countApply();
        
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->getUser()){
            // return $this->redirectToRoute('app_login');
            if (in_array('ROLE_CONSULTANT', $user->getRoles())) {
                return $this->redirectToRoute('consultant');
                dump(true);
                // $recruiter= $user->getRecruiters();
                // $name = $this->recruiterRepo->findOneByName($recruiter);
                // dump($name);
            } 
            
            if ( (in_array('ROLE_CANDIDATE', $user->getRoles())) || (in_array('ROLE_RECRUITER', $user->getRoles())) ){
// accéder à leur page directement                
                
                return $this->render('home/index.html.twig', [
                    'titlepage' => 'Page d\'accueil',
                    'countApplies'=> $countApply,
                    'countJobOffers'=> $countJobOffer,
                    // 'recruiters'=>$recruiter,
                ]);
            }
        } else {
            return $this->render('home/index.html.twig', [
                'titlepage' => 'Page d\'accueil',
                'countApplies' => $countApply,
                'countJobOffers' => $countJobOffer,
                // 'recruiters'=>$recruiter,
            ]);
        }
    }
    #[Route('/accueil', name: 'accueil')]
    public function index(): Response
    {
        $countJobOffer = $this->jobOfferRepo->countJobOffer();
        $countApply = $this->applyRepo->countApply();
        
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if($this->getUser()){
            
            } 
        return $this->render('home/index.html.twig', [
            'titlepage' => 'Page d\'accueil',
            'countApplies' => $countApply,
            'countJobOffers' => $countJobOffer,
            // 'recruiters'=>$recruiter,
            ]);
    }
    
}