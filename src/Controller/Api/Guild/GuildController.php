<?php

namespace App\Controller\Api\Guild;

use App\Entity\Guild;
use App\Entity\Squad;
use App\Repository\SquadRepository;
use App\Utils\Service\Extract\ExcelSquad;
use App\Utils\Manager\Guild as GuildManager;
use App\Utils\Manager\Squad as SquadManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Search\SquadType as SearchSquadType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/guild')]
class GuildController extends AbstractController
{
    public function __construct(
        private GuildManager $guildManager,
        private SquadManager $squadManager
    ) {
    }

    #[Route('/{id_swgoh}', name: 'api_guild', methods: ['GET'])]
    public function getGuildData(Guild $guild): JsonResponse
    {
        return $this->json(
            $this->guildManager->getGuildDataApi($guild)
        );
    }

    #[Route('/{id_swgoh}/squads', name: 'api_guild_squads', methods: ['GET'])]
    public function getGuildSquads(Guild $guild): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadDataByGuild($guild, false)
        );
    }

    #[Route('/{id_swgoh}/squad/get/{unique_identifier}', name: 'api_guild_squad', methods: ['GET'])]
    #[Route('/{id_swgoh}/squad/{unique_identifier}/export', name: 'api_guild_squad_export', methods: ['GET'])]
    #[ParamConverter('guild', options: ['mapping' => ['id_swgoh' => 'id_swgoh']])]
    #[ParamConverter('squad', options: ['mapping' => ['unique_identifier' => 'unique_identifier']])]
    public function getGuildSquadData(
        Request $request,
        Guild $guild,
        Squad $squad
    ): JsonResponse|BinaryFileResponse {
        if ($request->attributes->get('_route') == 'api_guild_squad') {
            return $this->json(
                $this->squadManager->getSquadDataPlayer($squad, $guild)
            );
        } else {
            $resultCreateFile = $this->squadManager
                ->generateExtractSquadDataPlayer($guild, $squad);
            return $this->generateFileResponse($resultCreateFile);
        }
    }

    #[Route('/{id_swgoh}/squad/search', name: 'api_guild_search_squads', methods: ['POST'])]
    #[Route('/{id_swgoh}/squad/export', name: 'api_guild_export_squads', methods: ['GET'])]
    #[ParamConverter('guild', options:['mapping' => ['id_swgoh' => 'id_swgoh']])]
    public function searchGuildSquad(
        Guild $guild = null,
        Request $request,
        SquadRepository $squadRepository,
        ExcelSquad $excelSquad
    ) {
        $routeName = $request->attributes->get('_route');
        $form = $this->createForm(SearchSquadType::class);
        $form->handleRequest($request);
        if ($routeName === 'api_guild_search_squads') {
            $form->submit($request->request->all());
        } else {
            $form->submit($request->query->all());
        }
        $formData = $form->getData();
        foreach ($formData as $key => $value) {
            if (empty($value)) {
                unset($formData[$key]);
            }
        }
        if ($routeName === 'api_guild_search_squads') {
            $resultFilter = $squadRepository->getGuildSquadByFilter($guild, $formData);
            return $this->json($resultFilter);
        } else {
            $resultFilter = $squadRepository->getGuildSquadByFilter($guild, $formData, false);
            if (!empty($resultFilter)) {
                $resultCreateFile = $excelSquad->constructSpreadShitViaSquads($guild, $resultFilter);
                return $this->generateFileResponse($resultCreateFile);
            }
        }
    }

    private function generateFileResponse(array $responseElements)
    {
        $reponse = new BinaryFileResponse($responseElements[0]);
        $reponse->headers->set('Content-Type', 'application/vnd.ms-excel');
        $reponse->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $responseElements[1]
        );
        $reponse->deleteFileAfterSend();
        return $reponse;
    }
}
