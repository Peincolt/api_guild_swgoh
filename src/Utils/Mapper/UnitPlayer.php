<?php

namespace App\Utils\Mapper;

use App\Dto\Api\UnitPlayer as UnitPlayerDto;
use App\Entity\Player as PlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;

abstract class UnitPlayer
{
    public static function fromDTO(
        UnitPlayerEntity $unitPlayer,
        UnitPlayerDto $unitPlayerDto,
        PlayerEntity $player = null,
        UnitEntity $unit = null
    ): UnitPlayerEntity {
        if (!empty($unitPlayer->getPlayer())) {
            $unitPlayer->setPlayer($player);
        }

        if (!empty($unitPlayer->getUnit())) {
            $unitPlayer->setUnit($unit);
        }

        $unitPlayer->setNumberStars($unitPlayerDto->number_stars);
        $unitPlayer->setLevel($unitPlayerDto->level);
        $unitPlayer->setGalacticalPower($unitPlayerDto->galactical_power);
        $unitPlayer->setSpeed($unitPlayerDto->speed);
        $unitPlayer->setLife($unitPlayerDto->life);
        $unitPlayer->setProtection($unitPlayerDto->protection);

        return $unitPlayer;
    }
}