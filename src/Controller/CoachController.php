<?php

namespace App\Controller;

use App\Entity\Coach;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CoachController extends AbstractController
{
    #[Route('/coach', name: 'app_coachpage')]
    public function coach(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Coach::class);
        $coachs = $repository->findAll();
        return $this->render('coach/index.html.twig', ['coachs' => $coachs]);
    }

    #[Route('/coach/{id<\d+>}', name: 'app_detail2page')]
    public function detailCoach(ManagerRegistry $doctrine,$id): Response // Correction du typehinting
    {
        $repository = $doctrine->getRepository(Coach::class); // Suppression de "persistentObject:"
        $coach = $repository->find($id);
if(!$coach){
    $coach>flush();
    $this->addFlash(
        'notice',
        "le coach n/'est plus disponible");
    return $this->redirectToRoute(route:'app_coachpage');
}
        return $this->render('coach/detail2.html.twig', ['coach' => $coach]); // Suppression de view:
    }
    }

