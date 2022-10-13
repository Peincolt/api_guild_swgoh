<?php

namespace App\Utils\Manager;

use App\Entity\Player;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Repository\HeroPlayerRepository;
use App\Repository\HeroRepository;

class HeroPlayer extends PlayerUnit
{
    public function __construct(
        private HeroPlayerRepository $heroPlayerRepository,
        private HeroRepository $heroRepository,
    ) {}

    public function createHeroPlayer(Player $player, array $data)
    {
        $hero = $this->heroRepository->findOneBy(
            [
                'base_id' => $data['base_id']
            ]
        );
        if ($hero) {
            $heroPlayer = $this->heroPlayerRepository
                ->findOneBy(
                    [
                        'unit' => $hero,
                        'player' => $player
                    ]
                );
            if (empty($heroPlayer)) {
                $heroPlayer = new HeroPlayerEntity();
                $heroPlayer->setUnit($hero);
                $heroPlayer->setPlayer($player);
            }
            $heroPlayer = $this->fillHeroPlayerEntity($heroPlayer, $data);
            $this->heroPlayerRepository->save($heroPlayer);
            return true;
        }
        return array('error_message' => 'Veuillez synchroniser les héros avant de synchroniser les données des joueurs.');
    }

    public function fillHeroPlayerEntity(HeroPlayerEntity $heroPlayer, array $data)
    {
        $heroPlayer = $this->fillUnitPlayer($heroPlayer, $data);
        $heroPlayer->setGearLevel($data['gear_level']);
        if ($data['gear_level'] == 13 && $data['relic_tier'] == 2) {
            $heroPlayer->setRelicLevel($data['relic_tier']);
        } elseif ($data['gear_level'] == 13 && $data['relic_tier'] >= 3) {
            $heroPlayer->setRelicLevel($data['relic_tier'] - 2);
        } else {
            $heroPlayer->setRelicLevel(0);
        }
        return $heroPlayer;
    }
}