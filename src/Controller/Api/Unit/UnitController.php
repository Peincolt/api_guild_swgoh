<?php

namespace App\Controller\Api\Unit;

use App\Entity\Hero;
use App\Entity\Ship;
use App\Entity\Unit;
use App\Repository\HeroRepository;
use App\Repository\ShipRepository;
use App\Controller\Api\ApiBaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UnitController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private HeroRepository $heroRepository,
        private ShipRepository $shipRepository
    ) {
    }
    
    #[Route('/heroes', name: 'api_heroes', methods: ['GET'])]
    public function getHeroes(HeroRepository $heroRepository): JsonResponse
    {
        return $this->json(
            array_map(
                fn($unit) => $this->serializeUnit($unit), 
                $heroRepository->findAll()
            )
        );
    }

    #[Route('/hero/{base_id}', name: 'api_hero', methods: ['GET'])]
    public function getHero(Hero $hero): JsonResponse
    {
        return $this->json(
            $this->serializeUnit($hero)
        );
    }

    #[Route('/ships', name: 'api_ships', methods: ['GET'])]
    public function getShips(ShipRepository $shipRepository): JsonResponse
    {
        return $this->json(
            array_map(
                fn($ship) => $this->serializeUnit($ship), 
                $shipRepository->findAll()
            )
        );  
    }

    #[Route('/ship/{base_id}', name:'api_ship', methods: ['GET'])]
    public function getShip(Ship $ship): JsonResponse
    {
        return $this->json(
            $this->serializeUnit($ship),
        );
    }

    #[Route('/units', name:'api_units', methods: ['GET'])]
    public function getUnits(HeroRepository $hero): JsonResponse
    {
        return $this->json(
            array_merge(
                [
                    'heroes' => array_map(
                        fn($hero) => $this->serializeUnit($hero), 
                        $this->heroRepository->findAll()
                    )
                ],
                [
                    'ships' => array_map(
                        fn($ship) => $this->serializeUnit($ship),
                        $this->shipRepository->findAll()
                    )
                ]
            )
        );
    }

    private function serializeUnit(Unit $unit): array
    {
        return $this->serializer->normalize(
            $unit,
            'json',
            [
                'groups' => ['api_unit']
            ]
        );
    }
}
