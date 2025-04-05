<?php

namespace App\Utils\Factory;

use App\Dto\Api\Hero as HeroDto;
use App\Dto\Api\Ship as ShipDto;
use App\Entity\Hero as HeroEntity;
use App\Entity\Ship as ShipEntity;
use App\Entity\Unit as UnitEntity;
use App\Repository\UnitRepository;
use App\Entity\Player as PlayerEntity;
use App\Repository\UnitPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Unit
{
    public function __construct(
        private ValidatorInterface $validator,
        private UnitRepository $unitRepository
    ){}

    /**
     * @return UnitEntity|array<string,string>
     */
    public function getEntityByApiResponse(array $apiResponse, string $classeName, EntityManagerInterface $entityManagerInterface)
    {
        switch ($classeName) {
            case "Hero":
                $dto = new HeroDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\Hero';
                $fullClassName = '\App\Entity\Hero';
                break;
            case "Ship":
                $dto = new ShipDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\Ship';
                $fullClassName = '\App\Entity\Ship';
                break;
            default:
                throw new \Exception('Une erreur est survenue lors de la mise à jour des unités. Cela est surement dû à un changement du format de l\'API');
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \Exception('Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API');
        }

        $unit = $entityManagerInterface->getRepository(UnitEntity::class)
            ->findOneBy(
                [
                    'base_id' => $dto->base_id
                ]
            );
        
        if (empty($unit)) {
            $unit = new $fullClassName();
            $entityManagerInterface->persist($unit);
        }
        
        return $classMapper::fromDto($unit, $dto);
    }
}