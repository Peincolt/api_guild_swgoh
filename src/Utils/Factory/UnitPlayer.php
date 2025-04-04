<?php

namespace App\Utils\Factory;

use App\Dto\Api\HeroPlayer as HeroPlayerDto;
use App\Dto\Api\ShipPlayer as ShipPlayerDto;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\Player as PlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\UnitPlayerRepository;
use App\Repository\UnitRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitPlayer
{
    public function __construct(
        private ValidatorInterface $validator,
        private UnitPlayerRepository $unitPlayerRepository,
        private UnitRepository $unitRepository
    ){}

    /**
     * @return UnitPlayerEntity|array<string,string>
     */
    public function getEntityByApiResponse(array $apiResponse, PlayerEntity $player)
    {
        switch ($apiResponse['data']['combat_type']) {
            case 1:
                $dto = new HeroPlayerDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\HeroPlayer';
                $classEntity = '\App\Entity\HeroPlayer';
                break;
            case 2:
                $dto = new ShipPlayerDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\ShipPlayer';
                $classEntity = '\App\Entity\ShipPlayer';
                break;
            default:
                return [
                    'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API'
                ];
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) === 0) {
            $unit = $this->unitRepository
                ->findOneBy(
                    [
                        'base_id' => $dto->id_swgoh
                    ]
                );
            if (!empty($unit)) {
                $unitPlayer = $this->unitPlayerRepository
                    ->findOneBy(
                        [
                            'player' => $player,
                            'unit' => $unit
                        ]
                    );
                if (empty($unitPlayer)) {
                    $unitPlayer = new $classEntity();
                }

                return $classMapper::fromDto($unitPlayer, $dto, $player, $unit);
            }
            return [
                'error_message' => 'L\'unité '.$dto->id_swgoh.' n\'a pas été retrouvée dans la base de données. Veuillez mettre à jour les unités avant de mettre à jour les informations des joueurs.'
            ];
        }
        return [
            'error_message' => 'Une erreur est survenue lors de la mise à jour des informations de l\'unité du joueur. Cela est surement dû à un changement du format de l\'API'
        ];
    }
}