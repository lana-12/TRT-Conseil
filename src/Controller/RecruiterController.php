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
    #[Route('/', name: 'recruiter')]
    public function index(): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'titlepage' => 'Recruiter',
        ]);
    }
    
    #[Route('/profil', name: 'profilR')]
    public function profil(Request $request,EntityManagerInterface $entityManager, SendMailService $mail,): Response
    {
        
        $recruiter = new Recruiter();

        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->addFlash('success', 'Votre demande a bien été pris en compte, un consultant doit valider votre compte pour déposer une annonce.');
            // $entityManager->persist($recruiter);
            // $entityManager->flush();
// dd($recruiter);
            // on envoi le mail
            // $mail->send(
            //     'no-reply@trt-conseil.fr',
            //     $recruiter->$this->getEmail(),
            //     'Activation de votre compte sur le site TRT Conseil',
            //     'register',
            //     // deux facon pour le context => array with le contenu du recruiter
            //     // [
            //     //     'recruiter'=> $recruiter
            //     // ] ou 
            //     compact('recruiter', 'token')
            // );
            // $this->addFlash('info', 'Votre demande a bien été pris en compte, lorsque votre compte sera actif vous recevrez un email de confirmation.');
        }




        
        return $this->render('recruiter/profil.html.twig', [
            'titlepage' => 'Profil',
            'recruiterForm'=> $form->createView(),
        ]);
    }
}