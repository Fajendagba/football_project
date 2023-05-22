<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class TeamsController extends AbstractController
{

    /**
     * @Route("/", name="teams")
     * @Method({"GET"})
     */
    public function index(Request $request, PersistenceManagerRegistry $doctrine, PaginatorInterface $paginator): Response
    {
        $teams = $doctrine->getRepository(Team::class)->findAll();


        $pagination = $paginator->paginate(
            $teams,
            $request->query->getInt('page', 1),
            2 // set to 2 per page
        );
    
        return $this->render('teams/index.html.twig', [
            'teams' => $pagination,
        ]);
    }

    
    /**
     * @Route("/teams/add", name="teams_add")
     * @Method({"POST"})
     */
    public function add(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('teams');
        }

        return $this->render('teams/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
