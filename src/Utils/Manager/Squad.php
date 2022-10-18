<?php

namespace App\Utils\Manager;

class Squad
{
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
}