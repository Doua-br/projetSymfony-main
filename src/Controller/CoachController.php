<?php
namespace App\Controller;

use App\Entity\Coach;
use App\Entity\AllUser;
use App\Form\CoachType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CoachController extends AbstractController
{
    #[Route('/coach', name: 'app_coachpage')]
    public function coach(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $repository = $entityManager->getRepository(Coach::class);
        $queryBuilder = $repository->createQueryBuilder('c')->getQuery();
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 6);

        return $this->render('coach/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/coach/{id<\d+>}', name: 'app_detail2page')]
    public function detailCoach(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(Coach::class);
        $coach = $repository->find($id);

        if (!$coach) {
            $this->addFlash('notice', "Le coach n'est plus disponible");
            return $this->redirectToRoute('app_coachpage');
        }

        return $this->render('coach/detail2.html.twig', ['coach' => $coach]);
    }

    #[Route('/coach/edit/{id?0}', name: 'coach.edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function addCoach(Coach $coach = null, ManagerRegistry $doctrine, Request $request, #[Autowire('%photo_dir%')] string $photoDir, EntityManagerInterface $entityManager): Response
    {
        $new = false;

        if (!$coach) {
            $new = true;
            $coach = new Coach();
        }

        $form = $this->createForm(CoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form['image']->getData();
            if ($photo) {
                $fileName = uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir, $fileName);
                $coach->setImage($fileName);
            }

            // Ensure email and password are set for new coach
            if ($new) {
                $coach->setEmail($form->get('email')->getData());
                $coach->setPassword($form->get('password')->getData());
            }

            $manager = $doctrine->getManager();
            $manager->persist($coach);

            if ($new) {
                $user = new AllUser();
                $user->setEmail($coach->getEmail());
                $user->setPassword($coach->getPassword());
                $user->setRoles(['ROLE_COACH']);
                $manager->persist($user);
            }

            $manager->flush();

            $this->addFlash('success', ($new ? 'Ajout' : 'Mise à jour') . ' effectué avec succès!');
            return $this->redirectToRoute('app_coachpage');
        }

        return $this->render('coach/editCoach.html.twig', [
            'form' => $form->createView(),
            'new' => $new
        ]);
    }

    #[Route('/coach/delete/{id}', name: 'coach.delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCoach(Coach $coach = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($coach) {
            $manager = $doctrine->getManager();
            $manager->remove($coach);
            $manager->flush();
            $this->addFlash('success', "Le coach a été supprimé avec succès");
        } else {
            $this->addFlash('error', "Coach inexistant");
        }
        return $this->redirectToRoute('app_coachpage');
    }
}
