<?php

namespace App\Controller\Api\Player;

use App\Entity\Player;
use App\Utils\Manager\Player as PlayerManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayerController extends AbstractController
{
    public function __construct(
        private PlayerManager $playerManager
    ) {

    }

    #[Route('/player/{id_swgoh}', name: 'api_player', methods: ['GET'])]
    public function getPlayerData(Player $player): JsonResponse
    {
        return $this->json(
            $this->playerManager->getPlayerDataApi($player)
        );
    }

    #[Route('/player/{id_swgoh}/heroes', name: 'api_player_heroes', methods: ['GET'])]
    public function getPlayerHeroes(Player $player): JsonResponse
    {
        return $this->json(
            $this->playerManager->getPlayerHeroesApi($player)
        );
    }

    #[Route('/player/{id_swgoh}/ships', name: 'api_player_ships', methods: ['GET'])]
    public function getPlayerShips(Player $player): JsonResponse
    {
        return $this->json(
            $this->playerManager->getPlayerShipsApi($player)
        );
    }
}
