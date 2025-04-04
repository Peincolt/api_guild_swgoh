<?php

namespace App\Utils\Mapper;

use App\Dto\Api\Unit as UnitDto;
use App\Entity\Unit as UnitEntity;

abstract class Unit
{
    public static function fromDTO(
        UnitEntity $unit,
        UnitDto $unitDto
    ): UnitEntity {
        $unit->setName($unitDto->name);
        $unit->setImage($unitDto->image);
        $unit->setCategories($unitDto->categories);
        $unit->setBaseId($unitDto->base_id);
        return $unit;
    }
}