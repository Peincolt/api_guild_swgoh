<?php

namespace App\Utils\Manager;

use App\Entity\Unit;
use App\Entity\Guild;
use App\Entity\SquadUnit;
use Symfony\Component\Form\Form;
use App\Entity\Squad as SquadEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\SerializerInterface;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Collections\Collection;

class Squad extends BaseManager
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SerializerInterface $serializer,
        private UnitPlayerManager $unitPlayerManager,
        private ValidatorInterface $validator
    ) {
        parent::__construct($entityManagerInterface);
        $this->setRepositoryByClass(SquadEntity::class);
    }

    public function getSquadDataByGuild(Guild $guild)
    {
        $arrayReturn = array();
        $squads = $this->getRepository()->getGuildSquad($guild);

        foreach ($squads as $squad) {
            $arrayReturn[$squad->getName()] = $this->getSquadData(
                $squad
            );
        }

        return $arrayReturn;
    }

    public function getSquadDataPlayer(SquadEntity $squad, Guild $guild)
    {
        $arrayReturn = $this->getSquadData($squad);

        foreach ($squad->getUnits() as $squadUnit) {
            $unit = $squadUnit->getUnit();
            foreach ($guild->getPlayers() as $player) {
                $arrayReturn[$squad->getName()]['units'][$unit->getBaseId()][$player->getName()] = $this->unitPlayerManager
                        ->getPlayerUnitByPlayerAndUnit(
                            $player,
                            $unit
                        );
            }
        }

        return $arrayReturn;
    }

    public function getSquadUnitsData(SquadEntity $squad)
    {
        $arrayReturn = $this->getSquadData($squad);
        foreach ($squad->getUnits() as $squadUnit) {
            $arrayReturn['units'][] = $this->serializer->normalize(
                $squadUnit->getUnit(),
                null,
                [
                    'groups' => [
                        'api_squad_unit'
                    ]
                ]
            );
        }

        return $arrayReturn;
    }

    public function getSquadData(SquadEntity $squad)
    {
        return $this->serializer->normalize(
            $squad,
            null,
            [
                'groups' => [
                    'api_squad'
                ]
            ]
        );
    }

    public function createSquadFromArray(array $params)
    {
        $arrayReturn = array();
        $squad = new SquadEntity();
        $this->getEntityManager()->persist($squad);
        $squad = $this->fillSquadFromArray($params);
        $errors = $this->validator->validate($squad);
        if (empty($errors) > 0) {
            $this->getEntityManager()->flush($squad);
            $arrayReturn['result']['message'] = 'L\'escouade a bien été créé';
        } else {
            foreach($errors as $error) {
                $arrayReturn['result']['errors'][] = $error->getMessage();
            }
        }
        return $arrayReturn;
    }

    /* Si me mec upload rien, ça plante ?! Voir pour modifier le code */
    /* Voir pour pas utiliser le trick du tableau $units */
    public function fillSquadByForm(SquadEntity $squad, Form $form)
    {
        $arrayUnit = new ArrayCollection();
        $arrayUnitToDelete = new ArrayCollection();
        $squadUnit = $this->getEntityManager()->persist($squad);
        $i = 0;
        if (!empty($form->get('guild')->getData())) {
            $squad->addGuild($form->get('guild')->getData());
        }
        if ($form->has('units')) {
            foreach ($form->get('units')->getData() as $unitBaseId) {
                $unit = $this->getEntityManager()
                    ->getRepository(Unit::class)->findOneBy(
                        [
                            'base_id' => $unitBaseId
                        ]
                    );

                if (!empty($squad->getId())) {
                    $squadUnit = $this->getEntityManager()
                        ->getRepository(SquadUnit::class)
                        ->findOneBy(
                            [
                                'unit' => $unit,
                                'squad' => $squad
                            ]
                        );
                    if (empty($squadUnit)) {
                        $existSquadUnit = false;
                    } else {
                        $existSquadUnit = true;
                    }
                
                } else {
                    $existSquadUnit = false;
                }

                if (!$existSquadUnit) {
                    $squadUnit = new SquadUnit();
                    $this->getEntityManager()->persist($squadUnit);
                    $squadUnit->setUnit($unit);
                    $squadUnit->setSquad($squad);
                    $squad->addUnit($squadUnit);
                }
                $squadUnit->setShowOrder($i);
                $arrayUnit->add($squadUnit);
                $i++;
            }

            $diff = array_diff(
                $squad->getUnits()->toArray(),
                $arrayUnit->toArray()
            );

            if (count($diff) > 0) {
                foreach ($diff as $unitDiff) {
                    $squad->removeUnit($unitDiff);
                }
            }

            if (!empty($squad->getId())) {
                $message = 'L\'escouade a bien été modifiée';
            } else {
                $message = 'L\'escouade a bien été ajoutée dans la base de données';
            }

            $this->getEntityManager()->flush();

            return array('result' => [
                'message' => $message,
                'unique_identifier' => $squad->getUniqueIdentifier()
                ]
            );
        } else {
            return array('result' => [
                'errors' => [
                    'Il faut au moins un membre afin de pouvoir créer une escouade'
                ]
            ]);
        }
    }

    public function generateExtract(Guild $guild, array $dataForm)
    {
        
    }
}