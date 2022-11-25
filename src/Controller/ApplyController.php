<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\User;
use App\Entity\Candidate;
use App\Entity\JobOffer;
use App\Repository\CandidateRepository;
use App\Repository\JobOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/candidature')]
class ApplyController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $em,
    ) {
    }
    
    #[Route('/', name: 'candidacy')]
    public function index(): Response
    {
        $repository = $this->doctrine->getRepository(Candidate::class);
        $candidates = $repository->findAll();

        
        return $this->render('apply/index.html.twig', [
            'titlepage' => 'Listes des Candidats',
            'candidates'=> $candidates,
        ]);
    }
    
    #[Route('/postuler/{id}', name: 'apply')]
    public function test(CandidateRepository $candidateRepo, JobOfferRepository $jobOfferRepo, int $id ): Response
    {
        /**
         * @var User $user
         */
        // Recup id_Candidate and email
        $user = $this->getUser();
            
        $apply = new Apply();
        
            //Recup id_joboffer
        $jobOffer = $jobOfferRepo->find($id);
        $apply->setJobOffer($jobOffer);
        $jobOffer->addApply($apply);
            
        //lier candidate à apply
        $candidates = $user->getCandidates();
        foreach ($candidates as $candidate) {
            $idCandidate = $candidate->getId();
            $candidate = $candidateRepo->find($idCandidate);
            $apply->setCandidate($candidate);
            $candidate->addApply($apply);
            dump($candidate);
        }
        $this->em->persist($apply);
        $this->em->flush();
        $this->addFlash('success', 'Votre candidature a bien été enregistrer.');
        
dump($apply);
dump($jobOffer);


        
        return $this->render('apply/test.html.twig', [
            'titlepage' => 'postulation à valider',
            'candidates'=> $candidates,
            'user'=> $user,
            
            
        ]);
    }
}