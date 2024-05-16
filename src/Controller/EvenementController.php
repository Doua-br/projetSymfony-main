<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;



class EvenementController extends AbstractController
{
    #[Route('/event', name: 'app_eventpage')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $repository = $entityManager->getRepository(Evenement::class);

        // Get the current page from the query parameters, default to 1 if not set
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 9; // Limit of events per page
        $offset = ($page - 1) * $limit;

        // Create a query to fetch events with pagination
        $query = $repository->createQueryBuilder('e')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query, true);
        $totalEvents = count($paginator);
        $totalPages = ceil($totalEvents / $limit);

        return $this->render('evenement/index.html.twig', [
            'events' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
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
    public function addEvent(Evenement $evenement = null, ManagerRegistry $doctrine, Request $request,#[Autowire('%photo_dir%') ]string  $photoDir2): Response{
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

    
    
     #[Route("/panier", name:"app_panier")]
    
    public function affichpanier(SessionInterface $session, EvenementRepository $eventsRepository)
    {
        //var panier
        $panier = $session->get("panier", []);

        // On "fabrique" les données(tableau)
        $dataPanier = [];
        

        foreach($panier as $id => $i){
            $event = $eventsRepository->find($id);
            $dataPanier[] = [
                "evenement" => $event
                
            ];
            
        }
        

        return $this->render('page/panier.html.twig', compact("dataPanier"));
    }

    
     #[Route("/panier/add/{id}", name:"add_panier")]
     
    public function add( SessionInterface $session,Evenement $event)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);

        $id = $event->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_eventpage");
    }

    
     #[Route("/panier/remove/{id}", name:"remove_panier")]
    public function remove(Evenement $event, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $event->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    
     #[Route("/panier/delete/{id}", name:"delete_panier")]
     
    public function delete(Evenement $event, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $event->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    
     #[Route("/panier/delete", name:"delete_all_panier")]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("app_eventpage");
    }


}
