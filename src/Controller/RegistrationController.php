<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


Class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function register(Request $request, User $user=null, EntityManagerInterface $em): Response
    {
        
        $user = new User();
        if ($user->getType() == 'candidate') {
            $user->setRoles(['ROLE_CANDIDATE']);
        } else{
            $user->setRoles(['ROLE_RECRUITER']);
        };

        $form = $this->createForm(RegistrationType::class, $user);

        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->addFlash('success', 'Le compte de connexion a bien été créé');
        };


        
        // dd($user);
        // dd($user->getRoles());
        return $this->render('registration/register.html.twig',[
            'formRegister'=> $form->createView(),
        ]);
    }
}