<?php

namespace App\Controller\Api\Squad;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Squad;
use App\Utils\Manager\Squad as SquadManager;

class SquadController extends AbstractController
{
    public function __construct(
        private SquadManager $squadManager,
        private SerializerInterface $serializer
    ) {
        
    }

    #[Route('/squad/{name}', name: 'api_squad', methods: ['GET'])]
    public function getSquad(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadData($squad)
        );
    }

    #[Route('/squad/{name}/units', name: 'api_squad_units', methods: ['GET'])]
    public function getSquadUnits(Squad $squad): JsonResponse
    {
        return $this->json(
            $this->squadManager->getSquadUnitsData($squad, true)
        );
    }
}