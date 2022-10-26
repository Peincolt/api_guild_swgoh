<?php

namespace App\Controller\Api\Player;

use App\Entity\Player;
use App\Controller\Api\ApiBaseController;
use App\Utils\Manager\Player as PlayerManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PlayerController extends AbstractController
{
    protected $serializer;

    #[Route('/player/{id_swgoh}', name: 'api_player', methods: ['GET'])]
    public function getHeroes(Player $player, PlayerManager $playerManager): JsonResponse
    {
        return $this->json(
            $playerManager->getPlayerDataApi($player)
        );
    }
}
