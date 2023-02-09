<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/test')]
class AdminController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        //mettre comment faire
        return $this->render('admin/index.html.twig', [
            // 'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/one', name: 'one')]
    public function user(): Response
    {
        //mettre comment faire
        return $this->render('admin/index.html.twig', [
            // 'controller_name' => 'AdminController',
        ]);
    }

    
}