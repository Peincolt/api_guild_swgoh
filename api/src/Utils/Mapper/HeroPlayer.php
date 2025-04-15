<?php

namespace App\Utils\Mapper;

use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Entity\Player as PlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Dto\Api\UnitPlayer as UnitPlayerDto;
use App\Utils\Mapper\UnitPlayer as UnitPlayerMapper;

class HeroPlayer extends UnitPlayerMapper
{
    public static function fromDTO(
        UnitPlayerEntity $heroPlayerEntity,
        UnitPlayerDto $heroPlayerDto,
        PlayerEntity $player = null,
        UnitEntity $unit = null
    ): HeroPlayerEntity {
        parent::fromDTO($heroPlayerEntity, $heroPlayerDto, $player, $unit);
        $heroPlayerEntity->setRelicLevel($heroPlayerDto->relic_level);
        $heroPlayerEntity->setGearLevel($heroPlayerDto->gear_level);
        return $heroPlayerEntity;
    }
}