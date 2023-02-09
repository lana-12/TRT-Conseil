<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\JobOffer;
use App\Entity\Recruiter;
use App\Form\JobOfferType;
use App\Form\RecruiterType;
use App\Repository\ApplyRepository;
use App\Repository\CandidateRepository;
use App\Service\SendMailService;
use App\Service\ArrayEmptyService;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Security("is_granted('ROLE_RECRUITER')", statusCode: 403)]
#[Route('/recruteur')]

class RecruiterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private  SendMailService $mail,
        private ArrayEmptyService $array,
    
    ){}
    
    #[Route('/', name: 'recruiter')]
    public function index(ManagerRegistry $doctrine, RecruiterRepository $repositoryRecruiter, $jobOffers=null, $candidatures=null ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            } 
            $recruiters = $user->getRecruiters();
            foreach($recruiters as $recruiter){

                $jobOffers = $recruiter->getJobOffers();
                if($jobOffers != null){
                    foreach($jobOffers as $jobOffer){
                        $candidatures = $jobOffer->getApplies();
                    }
                }else{
                    $jobOffers = null;
                }
                
            }
            // $repositoryRecruiter = $doctrine->getRepository(Recruiter::class);
            
            return $this->render('recruiter/index.html.twig', [
                'titlepage' => 'Mon Espace',
                'user'=>$user,
                'recruiters' =>$recruiters,
                'joboffers'=> $jobOffers,
                'candidatures'=> $candidatures,
        ]);
    }
    
    
    #[Route('/profil', name: 'profilR')]
    public function profil(Request $request, Recruiter $recruiter=null, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            }
        if(!$recruiter){
            $recruiter = new Recruiter();
            }
        $recruiter->setUser($this->getUser());
        $recruiter->setActive(true);            
        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($recruiter);
            $entityManager->flush();
            
            $this->addFlash('success', 'Profil complété, vous pouvez maintenant déposer une annonce.');
            return $this->redirectToRoute('recruiter');
        }
        return $this->render('recruiter/profil.html.twig', [
            'recruiterForm'=> $form->createView(),
            'editMode'=> $recruiter->getId() !== null,
            'recruiter'=> $recruiter

        ]);
    }
    
    #[Route('/modifier/{id}', name: 'editR')]
    public function editProfil(Request $request, Recruiter $recruiter=null): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            }
        if(!$recruiter){
            $recruiter = new Recruiter();
            }
        $recruiter->setUser($this->getUser());

        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if(!$recruiter->getId()){
                
            }
            $recruiter->setActive(true);            
            $this->em->persist($recruiter);
            $this->em->flush();
            
            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute('recruiter');
        }
    
        return $this->render('recruiter/profil.html.twig', [
            'recruiterForm'=> $form->createView(),
            'editMode'=> $recruiter->getId() !== null,
            'recruiter'=> $recruiter
        ]);
    }

    #[Route('/mesannonces', name: 'jobOffers')]
    public function jobOffer($jobOffers = null, $candidatures = null): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        
        }

        $recruiters = $user->getRecruiters();
            foreach ($recruiters as $recruiter){
                $jobOffers = $recruiter->getJobOffers();
                foreach($jobOffers as $jobOffer){
                    if(!$jobOffer->isActive()){
                        $this->addFlash('danger', 'Votre annonce de ' .$jobOffer->getTitle().' n\'a pas été validé.');
                    }
                    $candidatures = $jobOffer->getApplies();
                }

            }
            return $this->render('recruiter/myJobOffer.html.twig', [
                'titlepage' => 'Recruiter',
                'recruiters' => $recruiters,
                'joboffers' => $jobOffers,
                'candidatures'=> $candidatures,
                
            ]);

    }
}