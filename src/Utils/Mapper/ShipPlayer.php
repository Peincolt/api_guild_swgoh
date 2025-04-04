<?php

namespace App\Utils\Mapper;

use App\Entity\Player as PlayerEntity;
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Entity\Unit as UnitEntity;
use App\Dto\Api\UnitPlayer as UnitPlayerDto;
use App\Utils\Mapper\UnitPlayer as UnitPlayerMapper;

class ShipPlayer extends UnitPlayerMapper
{
    public static function fromDTO(
        UnitPlayerEntity $shipPlayerEntity,
        UnitPlayerDto $shipPlayerDto,
        PlayerEntity $player = null,
        UnitEntity $unit = null
    ): ShipPlayerEntity {
        return parent::fromDTO($shipPlayerEntity, $shipPlayerDto, $player, $unit);
    }
}