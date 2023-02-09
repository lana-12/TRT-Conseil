<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    
    #[Route('/contact', name: 'contact')]
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $manager, Request $request, SendMailService $mail): Response
    {

        /**
         * @var User $user 
         */
        $user = $this->getUser();
        $contact = new Contact();

        if ($this->getUser()) {
            $contact
                ->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();


            $manager->persist($contact);
            $manager->flush();        

            $mail->send(
                'no-reply@trt-conseil.fr',
                $user->getEmail(),
                'Nouveau message via le formulaire de contact',
                'contact',
                compact('user', 'contact')
            );

            $this->addFlash('info', 'Votre message a bien été envoyé, nous allons traiter votre demande.');
        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $form->createView(),
        ]);
    }
}