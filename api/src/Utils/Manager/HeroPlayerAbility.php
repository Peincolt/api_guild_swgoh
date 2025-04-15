<?php

namespace App\Utils\Manager;


use App\Repository\AbilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ability as AbilityEntity;
use App\Entity\HeroPlayerAbility as HeroPlayerAbilityEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\HeroPlayerAbilityRepository;

class HeroPlayerAbility
{
    public function __construct(
        private AbilityRepository $abilityRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository
    )
    {

    }

    public function setHeroPlayerOmicrons(
        UnitPlayerEntity $unitPlayer,
        array $data,
        EntityManagerInterface $entityManagerInterface
    ): bool {
        foreach ($data['omicron_abilities'] as $omicronAbilityName) {
            if (!is_string($omicronAbilityName)) {
                throw new \Exception('Une erreur est survenue lors de la mise à jours des capacités de l\'unité '.$unitPlayer->getUnit()->getName().' Une modification de l\'API a du être faite');
            }

            $omicronAbility = $entityManagerInterface->getRepository(AbilityEntity::class)
                ->findOneBy(
                    [
                        'hero' => $unitPlayer->getUnit(),
                        'base_id' => $omicronAbilityName
                    ]
                );

            if (empty($omicronAbility)) {
                throw new \Exception('Impossible de trouver la capacité '.$omicronAbilityName.' dans la base de données. Veuillez mettre à jour les unités puis les capacités avant de mettre à jour les joueurs');
            }

            $heroPlayerOmicronAbility = $entityManagerInterface->getRepository(HeroPlayerAbilityEntity::class)
                ->findOneby(
                    [
                        'ability' => $omicronAbility,
                        'heroPlayer' => $unitPlayer->getId()
                    ]
                );

            if (empty($heroPlayerOmicronAbility)) {
                $databaseHeroPlayerOmicronAbility = new HeroPlayerAbilityEntity();
                $entityManagerInterface->persist($databaseHeroPlayerOmicronAbility);
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
        }
        return true;
    }
}