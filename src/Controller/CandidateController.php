<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/candidate')]

class CandidateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ManagerRegistry $doctrine,
        private SendMailService $mail,
        private UserRepository $userRepo,
    ) {
    }
    #[Route('/', name: 'candidate')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            $repository = $this->doctrine->getRepository(Candidate::class);
            $candidates = $repository->findAll();

            
        }
        
                
        return $this->render('candidate/index.html.twig', [
            'titlepage' => 'Candidat',
            'candidates'=> $candidates,
        ]);
    }


    #[Route('/profil', name: 'profilC')]
    public function profil(Request $request, SluggerInterface $slugger): Response
    {

        
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
        $this->userRepo->findAll();
        $candidate = new Candidate();

        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                // $this->addFlash('success', 'Votre demande a bien été pris en compte, un consultant doit valider votre profil.');

                // $cvFile = $form->get('cv')->getData();
                // if ($cvFile) {
                // $cvFileName = $fileUploader->upload($cvFile);
                // $candidate->setCv($cvFileName);
                /**
                 * @var User $user
                 */
                // $candidates = $user->getCandidates();
                // foreach ($candidates as $candidate){
                //     $user->addCandidate($candidate);
                // }
                
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
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $candidate->setCv($newFilename);
                    
                }
            $candidate->getUser();
            
            // $this->em->persist($candidate);
            // $this->em->flush();
            dd($candidate);
            // on envoi le mail
            // $mail->send(
            //     'no-reply@trt-conseil.fr',
            //     $candidate->$this->getEmail(),
            //     'Activation de votre compte sur le site TRT Conseil',
            //     'register',
            //     // deux facon pour le context => array with le contenu du candidate
            //     // [
            //     //     'candidate'=> $candidate
            //     // ] ou 
            //     compact('candidate', 'token')
            // );
            
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