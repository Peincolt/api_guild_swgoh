<?php

namespace App\Utils\Manager;

use App\Entity\Guild;
use App\Entity\HeroPlayer;
use App\Entity\Squad;
use App\Repository\HeroPlayerAbilityRepository;
use App\Repository\SquadUnitRepository;
use App\Repository\UnitPlayerRepository;
use ReflectionClass;

class SquadUnit
{
    public function __construct(
        private SquadUnitRepository $squadUnitRepository,
        private UnitPlayerRepository $unitPlayerRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository
        ){}

    private function getGuildSquadUnits(Guild $guild, Squad $squad)
    {
        $arrayData = array();
        foreach ($squad->getUnits() as $squadUnit) {
            $unit = $squadUnit->getUnit();
            foreach ($guild->getPlayers() as $player) {
                $arrayData[$player->getName()][$unit->getName()] = $this->getUnitPlayerByPlayerAndUnit($unit, $player);
            }
        }
        return $arrayData;
    }

    private function getUnitPlayerByPlayerAndUnit(Unit $unit, Player $player)
    {
        $heroReflectionClass = new ReflectionClass($unit);
        $unitPlayerData = $this->unitPlayerRepository->findOneBy(
            [
                'unit' => $unit,
                'player' => $player
            ]
        );
        if (!empty($unitPlayerData)) {
            $arrayPlayerData = [
                'rarity' => $unitPlayerData->getNumberStars(),
                'level' => $unitPlayerData->getLevel(),
                'gear_level' => $unitPlayerData->getGearLevel(),
                'relic_level' => $unitPlayerData->getRelicLevel(),
                'speed' => $unitPlayerData->getSpeed(),
                'life' => $unitPlayerData->getLife(),
                'protection' => $unitPlayerData->getProtection()
            ];
            if ($heroReflectionClass->getShortName() == 'Hero') {
                $playerTwOmicrons = $this->getUnitPlayerOmicron($unitPlayerData);
                if (!empty($playerTwOmicrons)) {
                    foreach ($playerTwOmicrons as $omicron) {
                        $arrayPlayerData['omicron'][] = $omicron->getAbility()->getName();
                    }
                }
            }
        } else {
            $arrayReturn['name'] = 0;
            $arrayReturn['rarity'] = 0;
            $arrayReturn['level'] = 0;
            $arrayReturn['gear_level'] = 0;
            $arrayReturn['relic_level'] = 0;
            $arrayReturn['speed'] = 0;
            $arrayReturn['life'] = 0;
            $arrayReturn['protection'] = 0;
        }
    }

    public function getUnitPlayerOmicron(HeroPlayer $heroPlayer)
    {
        return $this->heroPlayerAbilityRepository
            ->getTwOmicron($heroPlayer);
    }
}