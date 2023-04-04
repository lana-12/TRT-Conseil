<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apply;
use App\Entity\JobOffer;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Repository\ApplyRepository;
use App\Repository\JobOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Security("is_granted('ROLE_CONSULTANT')", statusCode: 403)]
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
        
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }  else {
            
            return $this->render('consultant/index.html.twig', [
                // 'titlepage' => '',
            ]);
        }    
    }

    
    #[Route('/validation-compte', name: 'valid_account')]
    public function actifAccount(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {

            $accounts = $this->userRepo->findBy(['active' => false]);

            $accountRoles= $this->userRepo->findAll();
            foreach ($accountRoles as $count){
                $role= $count->getRoles();
                $roleStr = implode(" ", $role);
            }
        
            return $this->render('consultant/accountUser.html.twig', [
                'titlepage' => 'Comptes à valider',
                'accounts' => $accounts,
                'role'=> $roleStr,
            ]);
        }
    }

    #[Route('/activer/compte/{id}', name: 'active_user')]
    public function activedAccount(User $user): Response
    {      
    /**
     * @var User $user
     */
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');

        } else {

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
    }

    
    #[Route('/validation-offre', name: 'valid_jobOffer')]
    public function actifJobOffer(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {

            $jobOffers = $this->jobOfferRepo->findBy(['active' => false]);

            return $this->render('consultant/jobOfferActive.html.twig', [
            'titlepage' => 'Offres d\'emploi à valider',
            'jobOffers' => $jobOffers,
            ]);
        }
    }
        
    #[Route('/activer/offre/{id}', name: 'active_joboffer')]
    public function activedJobOffer(JobOffer $jobOffer): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {

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
    }

    
    #[Route('/validation-candidature', name: 'valid_apply')]
    public function actifApply(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {

            $applies = $this->applyRepo->findBy(['active' => false]);
        
            return $this->render('consultant/applyActive.html.twig', [
                'titlepage' => 'Candidatures à valider',
                'applies' => $applies,
            ]);
        }
    }
    
    #[Route('/activer/candidature/{id}', name: 'active_apply')]
    public function activedApply(Apply $apply): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        } else {
            $apply->setActive(true);
        
            $this->em->persist($apply);
            $this->em->flush();
            $this->addFlash('success', 'Candidature acceptée');
        
        //Envoi de email au recruteur 
            $mail = $apply->getJobOffer()->getRecruiter()->getUser()->getEmail();
            $recruiter = $apply->getJobOffer()->getRecruiter();
            $jobOffer = $apply->getJobOffer();
            $userId= $apply->getJobOffer()->getRecruiter()->getUser()->getId();
            $candidate = $apply->getCandidate();

        $this->mail->send(
            'no-reply@trt-conseil.fr',
            $mail,
            'Nouvelle Candidature',
            'recruiter',
            compact('recruiter', 'candidate', 'jobOffer'),
            $candidate->getCv(),
        );
            $this->addFlash('info', 'Un email a bien été envoyé au recruteur.');
            return $this->redirectToRoute('valid_apply');
        }
    }

    #[Route('/supprimer-candidature/{id}', name: 'remove_apply')]
    public function removeApply(Apply $apply, ApplyRepository $applyRepo): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }  else {

            $applyRepo->remove($apply);
        
            $this->em->flush();
            $this->addFlash('success', 'Candidature supprimé');
            
            //Envoi de Email au candidat
            $mail = $apply->getCandidate()->getUser()->getEmail();
            $mailCandidate = $apply->getCandidate()->getUser()->getEmail();
            $jobOffer = $apply->getJobOffer();
            $userId= $apply->getJobOffer()->getRecruiter()->getUser()->getId();
            $candidate = $apply->getCandidate();

            $this->mail->send(
                'no-reply@trt-conseil.fr',
                $mail,
                'Candidature Refusé',
                'removeApply',
                compact('candidate', 'jobOffer'),
            );
            $this->addFlash('info', 'Un email a bien été envoyé au candidat.');
            return $this->redirectToRoute('valid_apply');
        }
        
    }
}