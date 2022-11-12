<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        if ($user->getType() == 'candidate') {
            $user->setRoles(['ROLE_CANDIDATE']);

        } else{
            $user->setRoles(['ROLE_RECRUITER']);
        };
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->addFlash('success', 'Le compte de connexion a bien été créé');
            $entityManager->persist($user);
            $entityManager->flush();

            //On génère le JWT de l'user 
            // On crée le header
            $header = [
                'type'=>'JWT',
                'alg'=> 'HS256'
            ];
            
            //On crée le payload
            $payload =[
                'user_id'=> $user->getId()
            ];

            //On génère le token 
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
            
            // dd($token);
            
            
            
            // on envoi le mail
            $mail->send(
                'no-reply@trt-conseil.fr',
                $user->getEmail(),
                'Activation de votre compte sur le site TRT Conseil',
                'register',
                // deux facon pour le context => array with le contenu du user
                // [
                //     'user'=> $user
                // ] ou 
                compact('user', 'token')
            );
            $this->addFlash('info', 'Un email d\'Activation vous attend dans votre boîte Mail, cliquez sur le lien que vous avez reçu');


            // a rechercher c'est koi ???
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    #[Route('/verif/{token}', name: 'verify_user')]

    public function verifyUser($token, JWTService $jwt, UserRepository $userRepo, EntityManagerInterface $em):Response
    {
        // Au fur et à mesure on verifie avec les methode
        // dd($jwt->isValid($token)); //=> true
        //dd($jwt->getPayload($token)); //=> return a array avec n°id + 2 truc 
        // dd($jwt->isExpired($token)); //=> false
        // dd($jwt->check($token, $this->getParameter('app.jwtsecret'))); //=> true

        //On vérif si token est valide, n'a pas expiré et pas modifier
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret') )){
            //récup le payload
            $payload = $jwt->getPayload($token);
            
            //récupe le user du token
            $user = $userRepo->find($payload['user_id']);
            
            // On verif que user existe et n'a pas encore son compte
            if($user && !$user->isActive()){
                $user->setActive(true);
                $em->flush($user);

                $this->addFlash('success', 'Utilisateur activé');
                //Probleme il est bien rediriger la 
                return $this->redirectToRoute('profilR');
            }
        }
        //Ici un probleme se pose ds le token 
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name:'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepo): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if($user->isActive()){
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('profilR');
        }

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
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // on envoi le mail
        $mail->send(
            'no-reply@trt-conseil.fr',
            $user->getEmail(),
            'Activation de votre compte sur le site TRT Conseil',
            'register',
            // deux facon pour le context => array with le contenu du user
            // [
            //     'user'=> $user
            // ] ou 
            compact('user', 'token')
        );
        $this->addFlash('info', 'Un email d\'Activation vous attend dans votre boîte Mail');

        return $this->redirectToRoute('profilR');
    }
}