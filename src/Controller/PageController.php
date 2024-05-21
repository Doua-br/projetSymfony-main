<?php

namespace App\Controller;
use App\Form\ConnectAdminType;
use App\Entity\Administrateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            
        ]);
    }
    
    #[Route('/about', name: 'app_aboutpage')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
            
        ]);
    }
    #[Route('/contact', name: 'app_contactpage')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig', [
           
        ]);
    }
    
    
   
    
    
   
    
}
