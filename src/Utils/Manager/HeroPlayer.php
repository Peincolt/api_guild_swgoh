<?php

namespace App\Utils\Manager;

use App\Entity\Player;
use App\Entity\HeroPlayerAbility;
use App\Repository\HeroRepository;
use App\Utils\Manager\BaseManager;
use App\Repository\AbilityRepository;
use App\Repository\HeroPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Repository\HeroPlayerAbilityRepository;

class HeroPlayer extends UnitPlayer
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private HeroPlayerRepository $heroPlayerRepository,
        private HeroRepository $heroRepository,
        private AbilityRepository $abilityRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository
    ) {
        BaseManager::__construct($entityManagerInterface);
        $this->setRepositoryByClassName(HeroPlayerEntity::class);
    }

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
        if (count($data['omicron_abilities']) > 0) {
            foreach ($data['omicron_abilities'] as $omicronAbilityName) {
                $omicronAbility = $this->abilityRepository->findOneBy(
                    [
                        'hero' => $heroPlayer->getUnit(),
                        'base_id' => $omicronAbilityName
                    ]
                );

                if (!empty($heroPlayer->getId())) {
                    $heroPlayerOmicronAbility = $this->heroPlayerAbilityRepository
                        ->findOneby(
                            [
                                'ability' => $omicronAbility,
                                'heroPlayer' => $heroPlayer->getId()
                            ]
                        );
                    if (empty($heroPlayerOmicronAbility)) {
                        $databaseHeroPlayerOmicronAbility = new HeroPlayerAbility();
                        $this->heroPlayerAbilityRepository->save(
                            $databaseHeroPlayerOmicronAbility,
                            false
                        );
                        $databaseHeroPlayerOmicronAbility
                            ->setAbility($omicronAbility);
                        $databaseHeroPlayerOmicronAbility
                            ->setHeroPlayer($heroPlayer);
                        $databaseHeroPlayerOmicronAbility
                            ->setIsOmicronLearned(true);
                        foreach ($data['ability_data'] as $abilityData) {
                            if ($abilityData['id'] == $omicronAbilityName) {
                                $databaseHeroPlayerOmicronAbility
                                    ->setIsZetaLearned(($abilityData['is_zeta'] == 'true' ? true : false));
                            }
                        }
                    }
                }
            }
        }
        return $heroPlayer;
    }
}