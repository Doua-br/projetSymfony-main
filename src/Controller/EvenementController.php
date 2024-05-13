<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function detail(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Evenement::class);
        $event = $repository->find($id);
        if (!$event) {
            $this->addFlash('error', "L'événement n'est plus disponible");
            return $this->redirectToRoute('app_eventpage');
        }
        return $this->render('evenement/detail.html.twig', ['event' => $event]);
    }

    #[Route('/event/edit/{id?0}', name: 'event.edit')]
    public function addEvent(Evenement $evenement = null, ManagerRegistry $doctrine, Request $request, $photoDir2 = null): Response{
        $new = false;

        if (!$evenement) {
            $new = true;
            $evenement = new Evenement();
        }

        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form['image']->getData();
            if ($photo) {
                $fileName = uniqid() . '.' . $photo->guessExtension();
                $photo->move($photoDir2, $fileName);
            }
            $evenement->setImage($fileName);
            $manager = $doctrine->getManager();

            //l'objet est prêt d'être enregistré
            $manager->persist($evenement);
            //envoi
            $manager->flush();
            $this->addFlash(
                'success',
                ($new ? 'Ajout' : 'Mise à jour') . ' effectué avec succès!'
            );

            return $this->redirectToRoute('app_eventpage');
        }

        return $this->render('evenement/editEvent.html.twig', [
            'form' => $form->createView(),
            'new' => $new
        ]);
    }

    #[Route('/event/delete/{id}', name: 'event.delete')]
    public function deletePersonne(Evenement $evenement = null, ManagerRegistry $doctrine): RedirectResponse
    {
        // Récupérer la personne
        if ($evenement) {

            $manager = $doctrine->getManager();

            $manager->remove($evenement);

            $manager->flush();
            $this->addFlash('success', "L'evenement a été supprimé avec succès");
        } else {

            $this->addFlash('error', "event inexistante");
        }
        return $this->redirectToRoute('app_eventpage');
    }
}
