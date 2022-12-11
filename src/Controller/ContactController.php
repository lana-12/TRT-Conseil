<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContactController extends AbstractController
{
    public function __construct(
        private SendMailService $mail,
    ) {
    }
    
    #[Route('/contact', name: 'contact')]
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $manager, Request $request): Response
    {

        /**
         * @var User $user 
         */
        $user = $this->getUser();
        $contact = new Contact();
        // $today = new \DateTimeImmutable('now');
        // // $date = new \DateTime();
        // $contact->setCreatedAt($today);

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
dump($contact);
        
            $this->addFlash('info', 'Votre message a bien été envoyé');
        }
        // //Recup Partner.name === User.username
        // $partner = $partnerRepo->findOneByName($user->getUsername());
        // //Recup Structure.name === User.username
        // $structure = $structureRepo->findOneByName($user->getUsername());

        return $this->render('contact/index.html.twig', [
            'formContact' => $form->createView(),
            'contact' => $contact,
            // 'partners' => $partner,
            // 'structures' => $structure,
        ]);
    }
}