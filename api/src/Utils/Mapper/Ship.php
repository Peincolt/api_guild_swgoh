<?php

namespace App\Utils\Mapper;

use App\Dto\Api\Unit as UnitDto;
use App\Utils\Mapper\Unit as UnitMapper;
use App\Entity\Ship as ShipEntity;
use App\Entity\Unit as UnitEntity;

class Ship extends UnitMapper
{
    public static function fromDTO(
        UnitEntity $ship,
        UnitDto $shipDto
    ): ShipEntity {
        return parent::fromDto($ship, $shipDto);
    }
}