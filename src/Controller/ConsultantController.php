<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
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
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $em,
        private SendMailService $mail,
        private JWTService $jwt,
    ){}
    
    #[Route('/', name: 'consultant')]
    public function index(): Response
    {
    
        return $this->render('consultant/index.html.twig', [
            'titlepage' => 'Consultant',
        
        ]);
    }

    
    #[Route('/validation-compte', name: 'valid_account')]
    public function actifAccount(): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // } else {

        //     //Mettre tout le code que j'ai créer ci-dessous  ici

        // }
        $accounts =
        $this->userRepo->findBy(['active' => false]);

        
        return $this->render('consultant/accountUser.html.twig', [
            'titlepage' => 'Validez les comptes d\'utilisateurs',
            'accounts' => $accounts,
        ]);
    }

    
    #[Route('/activer/{id}', name: 'active')]
    public function actived(User $user): Response
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
}