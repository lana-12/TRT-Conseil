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
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            } 
        
            //if user->getRecruiters() ==""
            $recruiters = $user->getRecruiters();
            $this->array->arrayEmpty($recruiters);
            if($recruiters == true){
                $this->addFlash('alert', 'Vous devez mettre votre profil à jour.');
            return $this->redirectToRoute('profilR');
            }
            $profil='profil ok mettre description du recruteur';




        
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Mon Espace',
        ]);
    }
    
    
    #[Route('/profil', name: 'profilR')]
    public function profil(Request $request, Recruiter $recruiter=null): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            }
        
            
        if(!$recruiter){
            $recruiter = new Recruiter();
            }
        $recruiter->setUser($this->getUser());
        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $recruiter->setActive(true);            
            $this->em->persist($recruiter);
            $this->em->flush();
            
            $this->addFlash('success', 'Profil modifié, vous pouvez maintenant déposer une annonce.');
        }
    
        return $this->render('recruiter/profil.html.twig', [
            'titlepage' => 'Profil',
            'recruiterForm'=> $form->createView(),
        ]);
    }

    #[Route('/mesannonces', name: 'jobOffers')]
    public function index2(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
            // } elseif ($user->isActive() == false) {
            //     $this->addFlash('alert', 'Votre compte n\'est pas encore activé.');
            //     return $this->redirectToRoute('home');
        } 
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $recruiters = $user->getRecruiters();
        $this->array->arrayEmpty($recruiters);
        if($recruiters == true){
            $this->addFlash('alert', 'Vous devez mettre votre profil à jour.');
        return $this->redirectToRoute('profilR');
        }





        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
}