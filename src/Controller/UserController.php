<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;

use App\Entity\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {   
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    
    #[Route('/reserveEvent', name: 'reserveEvent')]
    public function add(Request $request,UserRepository $userRepository,EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class,$user);
        
        $form->remove('administrateur');
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original $task variable has also been updated
            $user = $form->getData();
            	
            $em->persist($user);
            $em->flush();
            $this->addFlash(
                'success',
                'reservation avec succes!'
            );

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_homepage');
        }
 
        return $this->render('evenement/reserveEvent.html.twig', ['form' => $form]);
    }       
}