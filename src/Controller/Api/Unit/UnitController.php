<?php

namespace App\Controller\Api\Unit;

use App\Entity\Hero;
use App\Entity\Ship;
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
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    
    #[Route('/heroes', name: 'api_heroes', methods: ['GET'])]
    public function getHeroes(HeroRepository $heroRepository): JsonResponse
    {
        return $this->json(
            array_map(
                function($unit)
                {
                    return $this->serializer->normalize($unit,'json',['groups' => ['api_unit']]);
                }, $heroRepository->findAll()
            )
        );
    }

    #[Route('/hero/{base_id}', name: 'api_hero', methods: ['GET'])]
    public function getHero(Hero $hero): JsonResponse
    {
        return $this->json(
            $this->serializer->normalize($hero,'json',['groups' => ['api_unit']])
        );
    }

    #[Route('/ships', name: 'api_ships', methods: ['GET'])]
    public function getShips(ShipRepository $shipRepository): JsonResponse
    {
        return $this->json(
            array_map(
                function($ship)
                {
                    return $this->serializer->normalize($ship,'json',['groups' => ['api_unit']]);
                }, $shipRepository->findAll()
            )
        );  
    }

    #[Route('/ship/{base_id}', name:'api_ship', methods: ['GET'])]
    public function getShip(Ship $ship): JsonResponse
    {
        return $this->json(
            $this->serializer->normalize($ship,'json',['groups' => ['api_unit']])
        );
    }
}
