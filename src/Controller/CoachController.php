<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Coach;
use App\Form\CoachType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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


#[Route('/coach/edit/{id?0}', name: 'coach.edit')]
public function addCoach(Coach $coach = null, ManagerRegistry $doctrine, Request $request,#[Autowire('%photo_dir%') ]string $photoDir): Response 
    {

    $new = false;
    
    if (!$coach) {
        $new = true;
        $coach = new Coach();
    }

    $form = $this->createForm(CoachType::class, $coach);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $photo=$form['image']->getData();
        if ($photo) {
            $fileName= uniqid().'.'. $photo->guessExtension();

            //$directory = $this->getParameter('personne_directory');
            //$personne->setImage($uploaderService->uploadFile($photo, $directory));
            $photo->move($photoDir,$fileName);
        }
        $coach->setImage($fileName);
        $manager=$doctrine->getManager();
        

        $manager=$doctrine->getManager();
        //l'objet est pret d'etre enregistrer
        $manager->persist($coach);
        //envoi
        $manager->flush();
        $this->addFlash(
            'success',
            ($new ? 'Ajout' : 'Mise à jour') . ' effectué avec succès!'
        );

        return $this->redirectToRoute('app_coachpage');
    }

    return $this->render('coach/editCoach.html.twig', [
        'form' => $form->createView(),
        'new' => $new
    ]);
}
#[
    Route('/coach/delete/{id}', name: 'coach.delete')
]
public function deletePersonne(Coach $coach = null, ManagerRegistry $doctrine): RedirectResponse {
    // Récupérer la personne
    if ($coach) {
       
        $manager = $doctrine->getManager();
        
        $manager->remove($coach);
        
        $manager->flush();
        $this->addFlash('success', "Le coach a été supprimé avec succès");
    } else {
      
        $this->addFlash('error', "coach innexistante");
    }
    return $this->redirectToRoute('app_coachpage');
}

}
