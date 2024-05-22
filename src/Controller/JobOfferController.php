<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Form\JobOfferType;
use App\Service\SendMailService;
use App\Service\ArrayEmptyService;
use App\Repository\JobOfferRepository;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/offre')]

class JobOfferController extends AbstractController
{
    public function __construct(
        private ArrayEmptyService $array,
        private SendMailService $mail,
        private EntityManagerInterface $em,
        private JobOfferRepository $jobOfferRepo,
    )
    {}
    #[Route('/', name: 'jobOffer')]
    public function index(): Response
    {
        $jobOffers = $this->jobOfferRepo->findBy(['active' => true]);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        
        return $this->render('jobOffer/index.html.twig', [
            'titlepage' => 'Offres d\'emploi',
            'joboffers'=> $jobOffers,
            'user'=> $user,
        ]);
    }

    #[Security("is_granted('ROLE_RECRUITER')", statusCode: 403)]
    #[Route('/poster', name: 'post')]
    public function newJobOffer(Request $request, RecruiterRepository $recruiterRepo, JobOffer $jobOffer = null): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('home');
        }

        $role = $user->getRoles();
        if (!in_array("ROLE_RECRUITER", $role, true)) {
            $this->addFlash('alert', 'Vous ne disposez pas des droits pour accéder à cette page');
            return $this->redirectToRoute('recruiter');
        }

        $recruiters = $user->getRecruiters();
        $jobOffer = new JobOffer();

            // $recruiters = $user->getRecruiters();
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
                $this->addFlash('success', 'Votre annonce est en cours de vérification, vous recevrez un email lorsqu\'elle sera active.');
            return $this->redirectToRoute('recruiter');
            }
        
        return $this->render('jobOffer/postJobOffer.html.twig', [
            'titlepage' => 'Poster une Annonce',
            'form' => $form->createView(),
            'recruiters'=> $recruiters,
            'editMode' => $jobOffer->getId() !== null,

        ]);
    }

    #[Security("is_granted('ROLE_RECRUITER')", statusCode: 403)]
    #[Route('/modifier/{id}', name: 'editP')]
    public function editJobOffer(Request $request, RecruiterRepository $recruiterRepo, JobOffer $jobOffer=null): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $recruiter = $recruiterRepo->find($jobOffer->getRecruiter()->getId());

        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $role = $user->getRoles();
        if (!in_array("ROLE_RECRUITER", $role, true)) {
            $this->addFlash('alert', 'Vous ne disposez pas des droits pour accéder à cette page');
            return $this->redirectToRoute('recruiter');
        }


        if($recruiter->getUser()->getId() !== $user->getId()) {
            $this->addFlash('alert', 'Cette annonce ne vous appartient pas !!');
            return $this->redirectToRoute('recruiter');
        }

        $recruiters = $user->getRecruiters();

            if(!$jobOffer){
                $jobOffer = new JobOffer();
            }

            $form = $this->createForm(JobOfferType::class, $jobOffer);
            $form->handleRequest($request);

            $jobOffer->setActive(false);

            if ($form->isSubmitted() && $form->isValid()) {


                $this->em->persist($jobOffer);
                $this->em->flush();
                $this->addFlash('success', 'Votre annonce a bien été modifier et doit être validé par un de nos consultants.');
            return $this->redirectToRoute('recruiter');
            }
        
        return $this->render('jobOffer/postJobOffer.html.twig', [
            'titlepage' => 'Modifier votre Annonce',
            'form' => $form->createView(),
            'recruiters'=> $recruiters,
            'editMode'=> $jobOffer->getId() !== null,
        ]);
    }

    #[Security("is_granted('ROLE_RECRUITER')", statusCode: 403)]
    #[Route('/supprimer/{id}', name: 'removeP')]
    public function removeJobOffer(JobOffer $jobOffer, JobOfferRepository $jobRepo): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('alert', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');

        } else {

            $jobRepo->remove($jobOffer);
            $this->em->flush();
            $this->addFlash('success', 'Votre annonce a bien été supprimé.');

            return $this->redirectToRoute('recruiter');
        }
    }
}