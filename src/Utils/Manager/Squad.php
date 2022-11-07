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

    public function getSquadDataByGuild(Guild $guild, bool $all)
    {
        $arrayReturn = array();
        $squads = $this->getRepository()->getGuildSquad($guild);

        foreach ($squads as $squad) {
            $arrayReturn[$squad->getName()] = $this->getSquadData(
                $squad,
                $guild,
                $all
            );
        }

        return $arrayReturn;
    }

    public function getSquadData(SquadEntity $squad, Guild $guild, bool $all)
    {
        $arrayReturn = $this->serializer->normalize(
            $squad,
            null,
            [
                'groups' => [
                    'api_guild_squad'
                ]
            ]
        );

        if ($guild && $all) {
            foreach ($squad->getUnits() as $squadUnit) {
                $unit = $squadUnit->getUnit();
                foreach ($guild->getPlayers() as $player) {
                    $arrayReturn[$squad->getName()]['units'][$unit->getName()][$player->getName()] = $this->unitPlayerManager
                            ->getPlayerUnitByPlayerAndUnit(
                                $player,
                                $unit
                            );
                }
            }
        }

        return $arrayReturn;
    }
}