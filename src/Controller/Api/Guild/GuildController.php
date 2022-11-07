<?php

namespace App\Controller\Api\Guild;

use App\Entity\Guild;
use App\Entity\Squad;
use App\Utils\Manager\Guild as GuildManager;
use App\Utils\Manager\Squad as SquadManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GuildController extends AbstractController
{
    public function __construct(
        private GuildManager $guildManager,
        private SquadManager $squadManager
    )
    {
        
    }

    #[Route('/guild/{id_swgoh}', name: 'api_guild', methods: ['GET'])]
    public function getGuildData(Guild $guild): JsonResponse
    {
        return $this->json(
            $this->guildManager->getGuildDataApi($guild)
        );
    }

    #[Route('/guild/{id_swgoh}/squads', name: 'api_guild_squads', methods: ['GET'])]
    public function getGuildSquads(Guild $guild): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadDataByGuild($guild, false)
        );
    }

    #[Route('/guild/{id_swgoh}/squad/{name}', name: 'api_guild_squad', methods: ['GET'])]
    #[ParamConverter('guild', options: ['mapping' => ['id_swgoh' => 'id_swgoh']])]
    #[ParamConverter('squad', options: ['mapping' => ['name' => 'name']])]
    public function getGuildSquadData(Guild $guild, Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadDataPlayer($squad, $guild)
        );
    }
}