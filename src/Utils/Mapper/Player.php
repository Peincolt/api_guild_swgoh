<?php

namespace App\Utils\Mapper;

use App\Dto\Api\Player as PlayerDto;
use App\Entity\Guild;
use App\Entity\Player as PlayerEntity;


class Player
{
    public static function fromDTO(PlayerEntity $player, PlayerDto $dto, Guild $guildEntity = null): PlayerEntity
    {
        $date = new \DateTime($dto->last_updated);

        if (!empty($guildEntity)) {
            $player->setGuild($guildEntity);
        }
        $player->setIdSwgoh($dto->id_swgoh);
        $player->setName($dto->name);
        $player->setLastUpdate($date);
        $player->setLevel($dto->level);
        $player->setGalacticalPower($dto->galactic_power);
        $player->setHeroesGalacticPower($dto->heroes_galactic_power);
        $player->setShipsGalacticPower($dto->ships_galactic_power);
        $player->setGearGiven($dto->gear_given);
        return $player;
    }
}