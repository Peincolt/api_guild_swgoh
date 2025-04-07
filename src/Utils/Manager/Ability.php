<?php

namespace App\Utils\Manager;

use App\Dto\Api\Ability as AbilityDto;
use App\Repository\HeroRepository;
use App\Utils\Service\Api\SwgohGg;
use App\Repository\AbilityRepository;
use App\Entity\Ability as AbilityEntity;
use App\Mapper\Ability as AbilityMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Ability
{
    public function __construct(
        private SwgohGg $swgohgg,
        private AbilityRepository $abilityRepository,
        private HeroRepository $heroRepository,
        private EntityManagerInterface $entityManagerInterface,
        private ValidatorInterface $validator
    ) {
    }

    public function updateAbilities(): mixed
    {
        $data = $this->swgohgg->fetchAbilities();
        if (isset($data['error_message_api_swgoh'])) {
            return $data;
        }
        $this->entityManagerInterface->beginTransaction();
        try {
            foreach ($data as $key => $arrayData) {
                if (
                    !is_array($arrayData) ||
                    !isset($arrayData['combat_type']) ||
                    !is_int($arrayData['combat_type'])
                ) {
                    throw new \Exception('Erreur lors de la synchronisation de l\'abilité '.$key.'. Une modification de l\'API a du être faite');
                }

                if ($arrayData['combat_type'] === 1) {
                    $abilityDto = new AbilityDto($arrayData);
                    $errors = $this->validator->validate($abilityDto);
                    if (count($errors) > 0) {
                        throw new \Exception('Erreur lors de la synchronisation de l\'abilité '.$key);
                    }

                    $ability = $this->abilityRepository->findOneBy(
                        [
                            'base_id' => $abilityDto->base_id
                        ]
                    );
                    $hero = $this->heroRepository->findOneBy(
                        [
                            'base_id' => $abilityDto->character_base_id
                        ]
                    );

                    if (empty($hero)) {
                        throw new \Exception('Erreur lors de la synchronisation de l\'abilité '.$key.'. Le héro '.$abilityDto->character_base_id.' n\'existe pas dans la base de données');
                    }

                    if (empty($ability)) {
                        $ability = new AbilityEntity();
                        $ability->setHero($hero);
                        $this->entityManagerInterface->persist($ability);
                    }
                    $ability = AbilityMapper::fromDto($ability, $abilityDto);
                }
            }
            $this->entityManagerInterface->flush();
            $this->entityManagerInterface->commit();
            return true;
        } catch (\Exception $e) {
            $this->entityManagerInterface->rollback();
            return [
                'error_message' => $e->getMessage()
            ];
        }
        return true;
    }
}