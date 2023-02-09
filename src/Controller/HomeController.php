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
    {}

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $countJobOffer = $this->jobOfferRepo->countJobOffer();
        $countApply = $this->applyRepo->countApply();

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($this->getUser()) {
            if (in_array('ROLE_CONSULTANT', $user->getRoles())) {
                return $this->redirectToRoute('consultant');
            }

            if ((in_array('ROLE_CANDIDATE', $user->getRoles())) || (in_array('ROLE_RECRUITER', $user->getRoles()))) {
                // accéder à leur page directement   A parametrer             

                return $this->render('home/index.html.twig', [
                    'titlebox1' => 'Qui nous sommes ?',
                    'titlebox2' => 'Comment Faire ?',
                    'countApplies' => $countApply,
                    'countJobOffers' => $countJobOffer,
                    // 'recruiters' => $recruiter,
                ]);
            }
        } else {
            return $this->render('home/index.html.twig', [
                'titlebox1' => 'Qui nous sommes ?',
                'titlebox2' => 'Comment Faire ?',
                'countApplies' => $countApply,
                'countJobOffers' => $countJobOffer,
                // 'recruiters' => $recruiter,
            ]);
        }
    }
    
    
}