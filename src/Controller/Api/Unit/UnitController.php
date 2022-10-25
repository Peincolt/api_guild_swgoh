<?php

namespace App\Controller\Api\Unit;

use App\Controller\Api\ApiBaseController;
use App\Repository\HeroRepository;
use App\Repository\UnitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UnitController extends ApiBaseController
{
    #[Route('/heroes', name: 'api_heroes', methods: ['GET'])]
    public function index(UnitRepository $heroRepository): JsonResponse
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
}
