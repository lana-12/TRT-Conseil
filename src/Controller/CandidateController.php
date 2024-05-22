<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apply;
use App\Entity\Candidate;
use App\Form\CandidateType;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Service\ArrayEmptyService;
use App\Repository\ApplyRepository;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Security("is_granted('ROLE_CANDIDATE')", statusCode: 403)]
#[Route('/candidat')]

class CandidateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ManagerRegistry $doctrine,
        private SendMailService $mail,
        private UserRepository $userRepo,
        private ArrayEmptyService $array,
    ) {
    }
    #[Route('/', name: 'candidate')]
    public function index($candidatures = null, $applies = null): Response
    {
        /**
         * @var User $user
         */
        $user= $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        } else{

            $candidates = $user->getCandidates();
            foreach ($candidates as $candidate) {
                $applies = $candidate->getApplies();
                foreach ($applies as $apply) {
                    if (!$apply->isActive()) {
                        $this->addFlash('danger', 'Votre candidature de ' . $apply->getJobOffer()->getTitle() . ' n\'a pas été validé par nos équipes.');
                    }
                    $candidatures = $apply->getJobOffer();
                }
            }
        }
        
        return $this->render('candidate/index.html.twig', [
            'titlepage' => 'Mon espace',
            'candidates'=> $candidates,
            'candidatures' => $candidatures,
            'applies' => $applies,
        ]);
    }


    #[Route('/profil', name: 'profilC')]
    public function profil(Request $request, SluggerInterface $slugger, Candidate $candidate=null, User $user=null): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $role = $user->getRoles();
        if (!in_array("ROLE_CANDIDATE", $role, true)) {
            $this->addFlash('alert', 'Vous ne disposez pas des droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }
            
        if (!$candidate) {
            $candidate = new Candidate();
        }
        $candidate->setUser($this->getUser());
        $candidate->setActive(true);

        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // A REVOIR INJECTION DE SERVICE DE MARCHE PAS
            
                // $cvFile = $form->get('cv')->getData();
                // if ($cvFile) {
                // $cvFileName = $fileUploader->upload($cvFile);
                // $candidate->setCv($cvFileName);
                
                $cvFile = $form->get('cv')->getData();
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($cvFile) {
                    $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

                    // Move the file to the directory where cvs are stored
                    try {
                        $cvFile->move(
                            $this->getParameter('cvs_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $this->addFlash('alert', 'Une erreur est survenue, dépôt de CV obligatoire !!');
                    }
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $candidate->setCv($newFilename);   
                }
            
            $this->em->persist($candidate);
            $this->em->flush();
            
            $this->addFlash('success', 'Profil modifié, vous pouvez maintenant postuler à une annonce');
            return $this->redirectToRoute('candidate');
        }
    
        return $this->render('candidate/profil.html.twig', [
            'titlepage' => 'Profil',
            'candidateForm' => $form->createView(),
            'candidate'=> $candidate,
            'editMode' => $candidate->getId() !== null,
        ]);
    }

    #[Route('/modifier/{id}', name: 'editCandidate')]
    public function editProfil(Request $request,SluggerInterface $slugger, Candidate $candidate = null): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }


        $role = $this->getUser()->getRoles();
        if (!in_array("ROLE_CANDIDATE", $role, true)) {
            $this->addFlash('alert', 'Vous ne disposez pas des droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        if (!$candidate) {
            $candidate = new Candidate();
        }
        $candidate->setUser($this->getUser());

            //Start methode1 + voir ci-dessous
        $candidate->setCV('');
            //end methode1

        
        //Revoir cette méthode2 + service uploadFile
        // $candidate->setCv(
        //     new File($this->getParameter('cvs_directory') . '/' . $candidate->getCv())
        // );

        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$candidate->getId()) {
            }
    //Start methode1
        $cvFile= $form->get('cv')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();
                try {
                    $cvFile->move(
                        $this->getParameter('cvs_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('alert', 'Une erreur est survenue, dépôt de CV obligatoire !!');
                }
                $candidate->setCv($newFilename);
            }

    // End methode1
            $candidate->setActive(true);
            $this->em->persist($candidate);
            $this->em->flush();

            $this->addFlash('success', 'Profil modifié');
            return $this->redirectToRoute('candidate');
        }

        return $this->render('candidate/profil.html.twig', [
            'candidateForm' => $form->createView(),
            'editMode' => $candidate->getId() !== null,
            'candidate' => $candidate
        ]);
    }

    #[Route('/tableau-de-bord', name: 'dashboard')]
    public function showDashboard(CandidateRepository $candidateRepo, $candidatures= null, $applies=null ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        } else {

            $candidates = $user->getCandidates();
            foreach ($candidates as $candidate){
                $applies = $candidate->getApplies();
                    foreach ($applies as $apply) {
                        if (!$apply->isActive()) {
                        $this->addFlash('danger', 'Votre candidature de ' . $apply->getJobOffer()->getTitle() . ' n\'a pas été validé par nos équipes.');
                        }
                    $candidatures = $apply->getJobOffer();    
                    }

            }
        }
        return $this->render('candidate/dashboard.html.twig', [
            'titlepage' => 'Mon tableau de bord',
            'candidates' => $candidates,
            'candidatures'=> $candidatures,
            'applies'=> $applies,
        ]);
    }

    #[Route('/supprimer-candidature/{id}', name: 'remove_candidature')]
    public function removeApply(Apply $apply, ApplyRepository $applyRepo): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        } else {

            $applyRepo->remove($apply);

            $this->em->flush();
            $this->addFlash('success', 'Candidature supprimé');
            return $this->redirectToRoute('dashboard');
        }
    }

}