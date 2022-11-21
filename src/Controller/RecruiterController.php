<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recruiter;
use App\Form\RecruiterType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/recruiter')]

class RecruiterController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private  SendMailService $mail,
    )
    {}
    #[Route('/', name: 'recruiter')]
    public function index(): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
    
    #[Route('/profil', name: 'profilR')]
    public function profil(Request $request, Recruiter $recruiter=null): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            
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
    }
        return $this->render('recruiter/profil.html.twig', [
            'titlepage' => 'Profil',
            'recruiterForm'=> $form->createView(),
        ]);
    }

    
    #[Route('/mesannonces', name: 'jobOffers')]
    public function createJobOffer(): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
}