<?php

namespace App\Utils\Manager;


use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Entity\HeroPlayerAbility as HeroPlayerAbilityEntity;
use App\Repository\AbilityRepository;
use App\Repository\HeroPlayerAbilityRepository;

class HeroPlayerAbility
{
    public function __construct(
        private AbilityRepository $abilityRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository
    )
    {

    }

    public function setHeroPlayerOmicrons(UnitPlayerEntity $unitPlayer, array $data)
    {
        foreach ($data['omicron_abilities'] as $omicronAbilityName) {
            if (is_string($omicronAbilityName)) {
                $omicronAbility = $this->abilityRepository->findOneBy(
                    [
                        'hero' => $unitPlayer->getUnit(),
                        'base_id' => $omicronAbilityName
                    ]
                );
                if ($omicronAbility) {
                    $heroPlayerOmicronAbility = $this->heroPlayerAbilityRepository
                    ->findOneby(
                        [
                            'ability' => $omicronAbility,
                            'heroPlayer' => $unitPlayer->getId()
                        ]
                    );

                    if (empty($heroPlayerOmicronAbility)) {
                        $databaseHeroPlayerOmicronAbility = new HeroPlayerAbilityEntity();
                        $this->heroPlayerAbilityRepository->save(
                            $databaseHeroPlayerOmicronAbility,
                            false
                        );
                        $databaseHeroPlayerOmicronAbility
                            ->setAbility($omicronAbility);
                        $databaseHeroPlayerOmicronAbility
                            ->setHeroPlayer($unitPlayer);
                        $databaseHeroPlayerOmicronAbility
                            ->setIsOmicronLearned(true);
                        if (
                            isset($data['ability_data']) &&
                            is_array($data['ability_data'])
                        ) {
                            foreach ($data['ability_data'] as $abilityData) {
                                if (is_array($abilityData)) {
                                    if (
                                        isset($abilityData['id']) &&
                                        is_string($abilityData['id']) &&
                                        $abilityData['id'] === $omicronAbilityName
                                    ) {
                                        $databaseHeroPlayerOmicronAbility
                                        ->setIsZetaLearned(
                                            ($abilityData['is_zeta'] == 'true' ? true : false)
                                        );
                                    }
                                }
                            }
                        }
                    }
                    return true;
                }
                return [
                    'error_message' => 'Impossible de mettre à jour les capacités omicron de l\'unité '.$unitPlayer->getUnit()->getName().' du joueur'
                ];
            } else {
                return [
                    'error_message' => 'Une erreur est survenue lors de la mise à jours des capacités de l\'unité. Une modification de l\'API a du être faite'
                ];
            }
        }
        return true;
    }
}