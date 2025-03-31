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
        if (!isset($data['error_message_api_swgoh'])) {
            $errorsMessages = ['error_messages' => []];
            $count = 0;
            foreach ($data as $key => $arrayData) {
                if (is_array($arrayData)) {
                    $abilityDto = new AbilityDto($arrayData);
                    $errors = $this->validator->validate($abilityDto);
                    if (count($errors) === 0) {
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
                        if (!empty($hero)) {
                            if (empty($ability)) {
                                $ability = new AbilityEntity();
                                $ability->setHero($hero);
                                $this->entityManagerInterface->persist($ability);
                            }
                            $ability = AbilityMapper::fromDto($ability, $abilityDto); 
                            if ($count >= 1000) {
                                $this->entityManagerInterface->flush();
                                $count = 0;
                            }
                            $count++;
                        } else {
                            $errorsMessages['error_messages'][] = 'Erreur lors de la synchronisation de l\'abilité '.$key.'. Le héro '.$abilityDto->character_base_id.' n\'existe pas dans la base de données';
                        }
                    } else  {
                        $errorsMessages['error_messages'][] = 'Erreur lors de la synchronisation de l\'abilité '.$key;
                    }
                }
            }
            $this->entityManagerInterface->flush();
            if (count($errorsMessages['error_messages']) > 0) {
                return $errorsMessages;
            }
            return true;
        }
        return $data;
    }
}