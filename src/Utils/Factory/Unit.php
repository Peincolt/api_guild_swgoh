<?php

namespace App\Utils\Factory;

use App\Dto\Api\Hero as HeroDto;
use App\Dto\Api\Ship as ShipDto;
use App\Entity\Hero as HeroEntity;
use App\Entity\Ship as ShipEntity;
use App\Entity\Player as PlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\UnitPlayerRepository;
use App\Repository\UnitRepository;
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
    public function getEntityByApiResponse(array $apiResponse, string $classeName)
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
                return [
                    'error_message' => 'Une erreur est survenue lors de la mise à jour des unités. Cela est surement dû à un changement du format de l\'API'
                ];
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) === 0) {
            $unit = $this->unitRepository
                ->findOneBy(
                    [
                        'base_id' => $dto->base_id
                    ]
                );
            if (empty($unit)) {
                $unit = new $fullClassName();
            }
            return $classMapper::fromDto($unit, $dto);
        }
        return [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API'
        ];
    }
}