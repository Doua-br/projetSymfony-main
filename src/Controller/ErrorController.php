<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController

{
    #[Route("/404", name:"page_not_found")]
    public function pageNotFound(): Response
    {
        return $this->render('page/404.html.twig', [], new Response('', 404));
    }
}
