<?php

namespace App\Mapper;

use App\Dto\Api\Guild as GuildDto;
use App\Entity\Guild as GuildEntity;


class Guild
{
    public static function fromDTO(GuildEntity $guild, GuildDto $dto): GuildEntity
    {
        $guild->setName($dto->name);
        $guild->setIdSwgoh($dto->id_swgoh);
        $guild->setGalacticPower($dto->galactic_power);
        $guild->setNumberPlayers($dto->number_players);
        return $guild;
    }
}