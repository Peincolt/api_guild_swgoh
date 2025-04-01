<?php

namespace App\Utils\Manager;

use App\Entity\Player;
use App\Repository\ShipRepository;
use App\Utils\Manager\BaseManager;
use App\Repository\ShipPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ShipPlayer as ShipPlayerEntity;

class ShipPlayer extends UnitPlayer
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private ShipPlayerRepository $shipPlayerRepository,
        private ShipRepository $shipRepository
    ) {
        BaseManager::__construct($entityManagerInterface);
        $this->setRepositoryByClass(ShipPlayerEntity::class);
    }

    /**
     * @return bool|array<string, string>
     */
    public function createShiplayer(Player $player, array $data): array|bool
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
            if (empty($shipPlayer)) {
                $shipPlayer = new ShipPlayerEntity();
                $shipPlayer->setUnit($ship);
                $shipPlayer->setPlayer($player);
            }
            $shipPlayer = $this->fillHeroPlayerEntity($shipPlayer, $data);
            $this->shipPlayerRepository->save($shipPlayer);
            return true;
        }
        return array(
            'error_message' => 'Veuillez synchroniser les héros avant de synchroniser les données des joueurs.'
        );
    }

    public function fillHeroPlayerEntity(ShipPlayerEntity $shipPlayer, array $data)
    {
        return $this->fillUnitPlayer($shipPlayer, $data);
    }
}