<?php

namespace App\Utils\Mapper;

use App\Dto\Api\Unit as UnitDto;
use App\Utils\Mapper\Unit as UnitMapper;
use App\Entity\Hero as HeroEntity;
use App\Entity\Unit as UnitEntity;

class Hero extends UnitMapper
{
    public static function fromDTO(
        UnitEntity $hero,
        UnitDto $heroDto
    ): HeroEntity {
        return parent::fromDto($hero, $heroDto);
    }
}