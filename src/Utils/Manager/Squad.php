<?php

namespace App\Utils\Manager;

use App\Entity\Guild;
use App\Entity\Squad as SquadEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use Symfony\Component\Serializer\SerializerInterface;

class Squad extends BaseManager
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SerializerInterface $serializer,
        private UnitPlayerManager $unitPlayerManager
    ) {
        parent::__construct($entityManagerInterface);
        $this->setRepositoryByClass(SquadEntity::class);
    }

    public function getSquadDataByGuild(Guild $guild)
    {
        $arrayReturn = array();
        $squads = $this->getRepository()->getGuildSquad($guild);

        foreach ($squads as $squad) {
            $arrayReturn[$squad->getName()] = $this->getSquadData(
                $squad
            );
        }

        return $arrayReturn;
    }

    public function getSquadDataPlayer(SquadEntity $squad, Guild $guild)
    {
        $arrayReturn = $this->getSquadData($squad);

        foreach ($squad->getUnits() as $squadUnit) {
            $unit = $squadUnit->getUnit();
            foreach ($guild->getPlayers() as $player) {
                $arrayReturn[$squad->getName()]['units'][$unit->getBaseId()][$player->getName()] = $this->unitPlayerManager
                        ->getPlayerUnitByPlayerAndUnit(
                            $player,
                            $unit
                        );
            }
        }

        return $arrayReturn;
    }

    public function getSquadUnitsData(SquadEntity $squad)
    {
        $arrayReturn = $this->getSquadData($squad);
        foreach ($squad->getUnits() as $squadUnit) {
            $arrayReturn['units'][] = $this->serializer->normalize(
                $squadUnit->getUnit(),
                null,
                [
                    'groups' => [
                        'api_squad_unit'
                    ]
                ]
            );
        }

        return $arrayReturn;
    }

    public function getSquadData(SquadEntity $squad)
    {
        return $this->serializer->normalize(
            $squad,
            null,
            [
                'groups' => [
                    'api_squad'
                ]
            ]
        );
    }
}