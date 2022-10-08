<?php

namespace App\Utils\Manager;

use App\Repository\HeroRepository;
use App\Utils\Service\Api\SwgohGg;
use App\Repository\AbilityRepository;
use App\Entity\Ability as AbilityEntity;
use Doctrine\ORM\EntityManagerInterface;

class Ability
{
    public function __construct(
        private SwgohGg $swgohgg,
        private AbilityRepository $abilityRepository,
        private HeroRepository $heroRepository,
        private EntityManagerInterface $entityManagerInterface
    )
    {}

    public function updateAbilities(): array|int
    {
        $data = $this->swgohgg->fetchAbilities();
        if (!isset($data['error_message'])) {
            $count = 0;
            foreach ($data as $key => $arrayData) {
                $ability = $this->abilityRepository->findOneBy(
                    [
                        'base_id' => $arrayData['base_id']
                    ]
                );
                if (empty($ability)) {
                    $ability = new AbilityEntity();
                    $hero = $this->heroRepository->findOneBy(
                        [
                            'base_id' => $arrayData['character_base_id']
                        ]
                    );
                    if (!empty($hero)) {
                        $ability->setHero($hero);
                        $this->entityManagerInterface->persist($ability);
                    }
                }
                $ability = $this->fillObjectWithArray($ability, $arrayData);
                if ($count >= 1000) {
                    $this->entityManagerInterface->flush();
                    $count = 0;
                }
                $count++;
            }
            $this->entityManagerInterface->flush();
            return true;
        }
        return $data;
    }

    public function fillObjectWithArray(
        AbilityEntity $ability,
        array $array
    ) :AbilityEntity {
        $ability->setBaseId($array['base_id']);
        $ability->setName($array['name']);
        $ability->setIsZeta($array['is_zeta']);
        $ability->setIsOmega($array['is_omega']);
        $ability->setIsOmicron($array['is_omicron']);
        $ability->setDescription($array['description']);
        $ability->setOmicronMode($array['omicron_mode']);
        return $ability;
    }
}