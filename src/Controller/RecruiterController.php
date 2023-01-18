<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\JobOffer;
use App\Entity\Recruiter;
use App\Form\JobOfferType;
use App\Form\RecruiterType;
use App\Repository\RecruiterRepository;
use App\Service\ArrayEmptyService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/recruteur')]

class RecruiterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private  SendMailService $mail,
        private ArrayEmptyService $array,
    ){}
    
    #[Route('/', name: 'recruiter')]
    public function index(RecruiterRepository $recruiterRepo): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            } 
        // $recruiters = $recruiterRepo->findAll();
            $recruiters = $user->getRecruiters();
            foreach ($recruiters as $recruiter){
                
                dump($recruiter->getJobOffers());
            }
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Mon Espace',
            'user'=>$user,
            'recruiters' =>$recruiters,
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
    dump($recruiter);
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
    public function jobOffer(RecruiterRepository $recruiterRepo): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            // } elseif ($user->isActive() == false) {
            //     $this->addFlash('danger', 'Votre compte n\'est pas encore activé.');
            //     return $this->redirectToRoute('home');
        } 
        
        $recruiter = $user->getRecruiters();
        $name = $recruiterRepo->findOneByName($recruiter);

        if ($name === null) {
            $this->addFlash('danger', 'Vous devez mettre votre profil à jour pour accéder à cette page.');
            return $this->redirectToRoute('profilR');
        } 
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
}