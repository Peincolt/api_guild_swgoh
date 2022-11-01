<?php

namespace App\Controller\Api\Guild;

use App\Entity\Guild;
use App\Utils\Manager\Guild as GuildManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GuildController extends AbstractController
{
    public function __construct(private GuildManager $guildManager)
    {
        
    }

    #[Route('/guild/{id_swgoh}', name: 'api_guild', methods: ['GET'])]
    public function getGuildData(Guild $guild): JsonResponse
    {
        return $this->json(
            $this->guildManager->getGuildDataApi($guild)
        );
    }
}