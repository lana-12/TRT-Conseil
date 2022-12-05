<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Service\ArrayEmptyService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function index(CandidateRepository $candidateRepo): Response
    {
        /**
         * @var User $user
         */
        $user= $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } 
        
        $candidate = $user->getCandidates();
        $name = $candidateRepo->findOneByName($candidate);
dump($name);
        if ($name == null) {
            $this->addFlash('danger', 'Vous devez mettre votre profil à jour.');
            return $this->redirectToRoute('profilC');
        } 
        
        $this->array->arrayEmpty($candidate);
        if($candidate === true){
            // $candidate = $user->getCandidates();
            $this->addFlash('alert', 'Vous devez mettre votre profil à jour.');
            return $this->redirectToRoute('profilC');
        }
            
            // dump($this->array->arrayEmpty($candidate));
        
        // dump($candidate);

        
        
        return $this->render('candidate/index.html.twig', [
            'titlepage' => 'Mon espace',
            // 'candidates'=> $candidates,
            // 'candidate'=> $candidate,
        ]);
    }


    #[Route('/profil', name: 'profilC')]
    public function profil(Request $request, SluggerInterface $slugger, Candidate $candidate=null, User $user=null): Response
    {        
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {

            
        if (!$candidate) {
            $candidate = new Candidate();
        }
        $candidate->setUser($this->getUser());

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
            $candidate->setActive(true);
            $this->em->persist($candidate);
            $this->em->flush();
            
            $this->addFlash('success', 'Profil modifié, vous pouvez maintenant postuler à une annonce');
        }
    }
        return $this->render('candidate/profil.html.twig', [
            'titlepage' => 'Profil',
            'candidateForm' => $form->createView(),
            // 'user'=> $user,
        ]);
    }
}