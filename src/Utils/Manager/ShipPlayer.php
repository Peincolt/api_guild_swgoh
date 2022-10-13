<?php

namespace App\Utils\Manager;

use App\Entity\Player;
use App\Repository\ShipRepository;
use App\Repository\ShipPlayerRepository;
use App\Entity\ShipPlayer as ShipPlayerEntity;

class ShipPlayer extends PlayerUnit
{
    public function __construct(
        private ShipPlayerRepository $shipPlayerRepository,
        private ShipRepository $shipRepository,
    ) {}

    public function createShiplayer(Player $player, array $data)
    {
        $ship = $this->shipRepository->findOneBy(
            [
                'base_id' => $data['base_id']
            ]
        );
        if ($ship) {
            $shipPlayer = $this->shipPlayerRepository
                ->findOneBy(
                    [
                        'unit' => $ship,
                        'player' => $player
                    ]
                );
            if (empty($heroPlayer)) {
                $shipPlayer = new ShipPlayerEntity();
                $shipPlayer->setUnit($ship);
                $shipPlayer->setPlayer($player);
            }
            $shipPlayer = $this->fillHeroPlayerEntity($shipPlayer, $data);
            $this->shipPlayerRepository->save($shipPlayer);
            return true;
        }
        return array('error_message' => 'Veuillez synchroniser les héros avant de synchroniser les données des joueurs.');
    }

    public function fillHeroPlayerEntity(ShipPlayerEntity $shipPlayer, array $data)
    {
        return $this->fillUnitPlayer($shipPlayer, $data);
    }
}