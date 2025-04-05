<?php

namespace App\Utils\Factory;

use App\Repository\UnitRepository;
use App\Entity\Player as PlayerEntity;
use App\Repository\UnitPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Api\HeroPlayer as HeroPlayerDto;
use App\Dto\Api\ShipPlayer as ShipPlayerDto;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UnitPlayer
{
    public function __construct(
        private ValidatorInterface $validator,
        private UnitPlayerRepository $unitPlayerRepository,
        private UnitRepository $unitRepository
    ){}

    /**
     * @return UnitPlayerEntity
     */
    public function getEntityByApiResponse(array $apiResponse, PlayerEntity $player, EntityManagerInterface $entityManagerInterface)
    {
        switch ($apiResponse['data']['combat_type']) {
            case 1:
                $dto = new HeroPlayerDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\HeroPlayer';
                $classEntity = '\App\Entity\HeroPlayer';
                break;
            case 2:
                die('ship');
                $dto = new ShipPlayerDto($apiResponse);
                $classMapper = '\App\Utils\Mapper\ShipPlayer';
                $classEntity = '\App\Entity\ShipPlayer';
                break;
            default:
                throw new \Exception('Une erreur est survenue lors de la mise à jour des informations de l\'unité. Cela est surement dû à un changement du format de l\'API');
                break;
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \Exception('Une erreur est survenue lors de la mise à jour des informations de l\'unité du joueur '.$player->getName(). '. Cela est surement dû à un changement du format de l\'API');
        }

        $unit = $entityManagerInterface->getRepository(UnitEntity::class)
            ->findOneBy(
                [
                    'base_id' => $dto->id_swgoh
                ]
            );
        
        if (empty($unit)) {
            throw new \Exception('L\'unité '.$dto->id_swgoh.' n\'a pas été retrouvée dans la base de données. Veuillez mettre à jour les unités avant de mettre à jour les informations des joueurs.');
        }

        $unitPlayer = $entityManagerInterface->getRepository(UnitPlayerEntity::class)
            ->findOneBy(
                [
                    'player' => $player,
                    'unit' => $unit
                ]
            );

        if (empty($unitPlayer)) {
            $unitPlayer = new $classEntity();
            $entityManagerInterface->persist($unitPlayer);
        }

        return $classMapper::fromDto($unitPlayer, $dto, $player, $unit);
    }
}