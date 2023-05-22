<?php

namespace App\Controller;

use App\Form\BuySellPlayerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PlayersController extends AbstractController
{
    #[Route('/players', name: 'app_players')]
    public function index(): Response
    {
        return $this->render('players/index.html.twig', [
            'controller_name' => 'PlayersController',
        ]);
    }

    // Action to buy/sell a player
    #[Route('/players/buy-sell', name: 'players_buy_sell')]
    public function buySell(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(BuySellPlayerType::class);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $playerId = $data['player'];
            $amount = $data['amount'];

            $entityManager = $doctrine->getManager();
            $player = $entityManager->getRepository(Player::class)->find($playerId);

            if ($player) {
                $player->setBalance($player->getBalance() + $amount);
                $entityManager->flush();

                // Redirect to a success page or perform any other necessary actions
                return $this->redirectToRoute('teams');
            }
        }

        return $this->render('players/buy_sell.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}