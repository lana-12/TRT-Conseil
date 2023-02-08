<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\JobOffer;
use App\Entity\User;
use App\Repository\ApplyRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Repository\JobOfferRepository;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/consultant')]

class ConsultantController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepo,
        private JobOfferRepository $jobOfferRepo,
        private ApplyRepository $applyRepo,
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $em,
        private SendMailService $mail,
        private JWTService $jwt,
    ){}
    
    #[Route('/', name: 'consultant')]
    public function index(): Response
    {
        // if ($this->getUser() === ) {

            
        //     return $this->redirectToRoute('app_login');
        // } else {

        
        // }

    
        return $this->render('consultant/index.html.twig', [
            // 'titlepage' => '',
        
        ]);
    }

    
    #[Route('/validation-compte', name: 'valid_account')]
    public function actifAccount(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // } else {

        //     //Mettre tout le code que j'ai créer ci-dessous  ici
        $accounts = $this->userRepo->findBy(['active' => false]);

        $counts= $this->userRepo->findAll();
        
        foreach ($counts as $count){
            $role= $count->getRoles();
            dump($role[0]);
            $roleStr = implode(" ", $role);
        }
        
        return $this->render('consultant/accountUser.html.twig', [
            'titlepage' => 'Validez les comptes d\'utilisateurs',
            'accounts' => $accounts,
            'role'=> $roleStr,
        ]);
    }

    
    #[Route('/activer/compte/{id}', name: 'active_user')]
    public function activedAccount(User $user): Response
    {      
    /**
     * @var User $user
     */
        $user->setActive(true);
        $this->em->flush();
        $this->addFlash('success', 'Compte activé');

        //On génère le JWT de l'user 
        // On crée le header
        $header = [
            'type' => 'JWT',
            'alg' => 'HS256'
        ];

        //On crée le payload
        $payload = [
            'user_id' => $user->getId()
        ];

        //On génère le token 
        $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // dd($token);

        $this->mail->send(
            'no-reply@trt-conseil.fr',
            $user->getEmail(),
            'Activation de votre compte sur le site TRT Conseil',
            'activeAccount',
            compact('user', 'token')
        );
        $this->addFlash('info', 'Un email d\'Activation a bien été envoyé.');
        return $this->redirectToRoute('valid_account');
    }


    

    #[Route('/validation-offre', name: 'valid_jobOffer')]
    public function actifJobOffer(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // } else {

        //     //Mettre tout le code que j'ai créer ci-dessous  ici
        $jobOffers = $this->jobOfferRepo->findBy(['active' => false]);
        
        // }

        return $this->render('consultant/jobOfferActive.html.twig', [
            'titlepage' => 'Validez les offres d\'emploi',
            'jobOffers' => $jobOffers,
        ]);
    }
    
    #[Route('/activer/offre/{id}', name: 'active_joboffer')]
    public function activedJobOffer(JobOffer $jobOffer): Response
    {
        $jobOffer->setActive(true);
        $mail = $jobOffer->getRecruiter()->getUser()->getEmail();
        $name = $jobOffer->getRecruiter()->getNameCompany();
        $userId= $jobOffer->getRecruiter()->getUser()->getId();
        $this->em->flush();
        $this->addFlash('success', 'Annonce activée');

        // On génère le JWT de l'user 
        // On crée le header
        $header = [
            'type' => 'JWT',
            'alg' => 'HS256'
        ];

        //On crée le payload
        $payload = [
            'user_id' => $userId
        ];

        //On génère le token 
        $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // dd($token);

        $this->mail->send(
            'no-reply@trt-conseil.fr',
            $mail,
            'Votre Annonce a bien été activé',
            'activeJobOffer',
            compact('name', 'token')
        );
        $this->addFlash('info', 'Un email d\'Activation a bien été envoyé.');
        return $this->redirectToRoute('valid_jobOffer');
    }

    
    #[Route('/validation-candidature', name: 'valid_apply')]
    public function actifApply(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // } else {

        //     //Mettre tout le code que j'ai créer ci-dessous  ici
        $applies = $this->applyRepo->findBy(['active' => false]);
        
dump($applies);
        // }
    
        return $this->render('consultant/applyActive.html.twig', [
            'titlepage' => 'Validez les postulants',
            'applies' => $applies,
        ]);
    }
    
    #[Route('/activer/candidature/{id}', name: 'active_apply')]
    public function activedApply(Apply $apply): Response
    {
        // $apply = $this->applyRepo->find($id);
        $apply->setActive(true);
        $mail = $apply->getJobOffer()->getRecruiter()->getUser()->getEmail();
        $recruiter = $apply->getJobOffer()->getRecruiter();
        $jobOffer = $apply->getJobOffer();
        $userId= $apply->getJobOffer()->getRecruiter()->getUser()->getId();
        $candidate = $apply->getCandidate();
        $this->em->persist($apply);
        $this->em->flush();
        $this->addFlash('success', 'Annonce activée');

        // // On génère le JWT de l'user 
        // // On crée le header
        // $header = [
        //     'type' => 'JWT',
        //     'alg' => 'HS256'
        // ];

        // //On crée le payload
        // $payload = [
        //     'user_id' => $userId
        // ];

        // //On génère le token 
        // $token = $this->jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // dd($token);

        $this->mail->send(
            'no-reply@trt-conseil.fr',
            $mail,
            'Candidature',
            'recruiter',
            compact('recruiter', 'candidate', 'jobOffer'),
            $candidate->getCv(),
        );
        $this->addFlash('info', 'Un email a bien été envoyé au recruteur.');
        return $this->redirectToRoute('valid_apply');
        
    }
}