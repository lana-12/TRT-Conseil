<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Form\JobOfferType;
use App\Repository\RecruiterRepository;
use App\Service\ArrayEmptyService;
use App\Service\SendMailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/offre')]

class JobOfferController extends AbstractController
{
    public function __construct(
        private ArrayEmptyService $array,
        private SendMailService $mail,
    )
    {}
    #[Route('/', name: 'jobOffer')]
    public function index(): Response
    {
        return $this->render('jobOffer/index.html.twig', [
            'titlepage' => 'Offres d\'emploi',
        ]);
    }


    #[Route('/poster', name: 'post')]
    public function newJobOffer(Request $request, RecruiterRepository $recruiterRepo): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }
        
        $recruiters = $user->getRecruiters();
        $this->array->arrayEmpty($recruiters);
        if($recruiters == true){
            $this->addFlash('alert', 'Vous devez mettre votre profil à jour.');
        }
            $jobOffer = new JobOffer();

            $recruiters = $user->getRecruiters();
            foreach ($recruiters as $recruiter) {
                $id = $recruiter->getId();
                $recruiter = $recruiterRepo->find($id);
                $jobOffer->setRecruiter($recruiter);
                $recruiter->addJobOffer($jobOffer);
            }

            $form = $this->createForm(JobOfferType::class, $jobOffer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $this->em->persist($jobOffer);
                $this->em->flush();
            // on envoi le mail
            // $this->mail->send(
            //     'no-reply@trt-conseil.fr',
            //     $user->getEmail(),
            //     'Création de compte sur le site TRT Conseil',
            //     'register',
            //     [
            //         'user' => $user,
            //         'token' => null,
            //     ]
            // );
                $this->addFlash('success', 'Votre annonce est en cours de vérification, vous recevrez un email lorsqu\'elle sera active.');
            }
        
        return $this->render('jobOffer/newJobOffer.html.twig', [
            'titlepage' => 'Poster une Annonce',
            'form' => $form->createView(),
        ]);
    }
}