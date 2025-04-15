<?php

namespace App\Mapper;

use App\Dto\Api\Ability as AbilityDto;
use App\Entity\Ability as AbilityEntity;


class Ability
{
    public static function fromDTO(AbilityEntity $ability, AbilityDto $dto): AbilityEntity
    {
        $ability->setName($dto->name);
        $ability->setBaseId($dto->base_id);
        $ability->setIsZeta($dto->is_zeta);
        $ability->setIsOmega($dto->is_omega);
        $ability->setIsOmicron($dto->is_omicron);
        $ability->setDescription($dto->description);
        $ability->setOmicronMode($dto->omicron_mode);
        return $ability;
    }
}