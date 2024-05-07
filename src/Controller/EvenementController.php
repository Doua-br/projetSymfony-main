<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Evenement;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvenementController extends AbstractController
{
    #[Route('/event', name: 'app_eventpage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Evenement::class);
        $events = $repository->findAll();
        return $this->render('evenement/index.html.twig', ['events' => $events]);
    }
    

    #[Route('/event/{id<\d+>}', name: 'app_detailpage')]
    public function detail(ManagerRegistry $doctrine,$id): Response // Correction du typehinting
    {
        $repository = $doctrine->getRepository(Evenement::class); // Suppression de "persistentObject:"
        $event = $repository->find($id);
            if(!$event){
                $this->addFlash('error', "l'événement n'est plus disponible");
                return $this->redirectToRoute(route:'app_eventpage');
            }
        return $this->render('evenement/detail.html.twig', ['event' => $event]); // Suppression de view:
    }





}
