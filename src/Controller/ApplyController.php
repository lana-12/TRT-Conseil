<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apply;
use App\Entity\JobOffer;
use App\Entity\Candidate;
use App\Repository\JobOfferRepository;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/candidature')]
class ApplyController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $em,
    ) {
    }
    /**
     * La liste de tous les candidats
     */
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

    #[Security("is_granted('ROLE_CANDIDATE')", statusCode: 403)]
    #[Route('/postuler/{id}', name: 'apply')]
    public function test(CandidateRepository $candidateRepo, JobOfferRepository $jobOfferRepo, int $id, ManagerRegistry $doctrine ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $candidates = $user->getCandidates();

        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $role = $user->getRoles();
        if (!in_array("ROLE_CANDIDATE", $role, true)) {
            $this->addFlash('alert', 'Vous ne disposez pas des droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        
        $apply = new Apply();
        $jobOffer = $jobOfferRepo->find($id);
        $apply->setJobOffer($jobOffer);
        $jobOffer->addApply($apply);
        
        foreach ($candidates as $candidate) {
                $idCandidate = $candidate->getId();
                $candidate = $candidateRepo->find($idCandidate);
                $apply->setCandidate($candidate);
                $candidate->addApply($apply);
            } 

        $candApply = $apply->getCandidate();
        if($candApply === null){
            $this->addFlash('danger', 'Vous ne pouvez pas postuler à cette annonce, votre profil n\'est pas à jour.');
            return $this->redirectToRoute('profilC');

        }else{
            $this->em->persist($apply);
            $this->em->flush();
            $this->addFlash('success', 'Votre candidature a bien été enregistrer.');
            
            return $this->redirectToRoute('jobOffer');
        }        
    }

    
}