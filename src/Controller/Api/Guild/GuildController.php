<?php

namespace App\Controller\Api\Guild;

use App\Entity\Guild;
use App\Entity\Squad;
use App\Repository\SquadRepository;
use App\Utils\Manager\Guild as GuildManager;
use App\Utils\Manager\Squad as SquadManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Search\SquadType as SearchSquadType;
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

    #[Route('/guild/{id_swgoh}/squad/{unique_identifier}', name: 'api_guild_squad', methods: ['GET'])]
    #[ParamConverter('guild', options: ['mapping' => ['id_swgoh' => 'id_swgoh']])]
    #[ParamConverter('squad', options: ['mapping' => ['unique_identifier' => 'unique_identifier']])]
    public function getGuildSquadData(Guild $guild, Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadDataPlayer($squad, $guild)
        );
    }

    #[Route('/guild/{id_swgoh}/squad/search', name: 'api_guild_search_squad', methods: ['POST'])]
    #[Route('/guild/{id_swgoh}/squad/export', name: 'api_guild_export_squad', methods: ['POST'])]
    #[ParamConverter('guild', options:['mapping' => ['id_swgoh' => 'id_swgoh']])]
    public function searchGuildSquad(Guild $guild, Request $request, SquadRepository $squadRepository)
    {
        $form = $this->createForm(SearchSquadType::class);
        $form->handleRequest($request);
        $form->submit($request->request->all());
        $formData = $form->getData();
        foreach($formData as $key => $value) {
            if (empty($value)) {
                unset($formData[$key]);
            }
        }
        if ($request->attributes->get('_route') == 'api_guild_search_squad') {
            return $this->json($squadRepository->getGuildSquadByFilter($guild, $formData));
        } else {
            die('extract');
        }
    }
}