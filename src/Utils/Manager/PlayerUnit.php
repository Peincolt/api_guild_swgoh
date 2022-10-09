<?php

namespace App\Utils\Manager;

use App\Entity\Player;
use App\Repository\HeroPlayerAbilityRepository;
use App\Repository\HeroPlayerRepository;
use App\Repository\ShipPlayerRepository;
use App\Repository\UnitPlayerRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PlayerUnit 
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private HeroPlayerRepository $heroPlayerRepository,
        private ShipPlayerRepository $shipPlayerRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository,
        private UnitRepository $unitRepository,
        private UnitPlayerRepository $unitPlayerRepository
    ) {}

    public function getPlayerUnit(array $data, Player $player, string $type)
    {
        $unit = $this->unitRepository->findOneBy(
            [
                'base_id' => $data['base_id']
            ]
        );

        if (!empty($unit)) {
            $unitPlayer = $this->unitPlayerRepository->findOneBy(
                [
                    'player' => $player,
                    'unit' => $unit
                ]
            );
            if (empty($unitPlayer)) {
                switch ($type) {

                }
            }
        }
        return array(
            'error_message' => 'Veuillez mettre à jour les unités avant de mettre à jour les données des joueurs'
        );

        try {
            $baseUnitEntityName = "\App\Entity\\".ucfirst($type);
            $entityName = $baseUnitEntityName."Player";
            $fonctionName = 'set'.ucfirst($type);
            if ($baseUnit = $this->_dataHelper->getDatabaseData($baseUnitEntityName, array('base_id' => $data['base_id']))) {
                if (!$playerUnit = $this->_dataHelper->getDatabaseData($entityName, array('player' => $player, $type => $baseUnit))) {
                    $playerUnit = new $entityName;
                    $playerUnit->setPlayer($player);
                    $this->_entityManagerInterface->persist($playerUnit);
                }
                $playerUnit->$fonctionName($baseUnit);
                $this->_dataHelper->fillObject($data, 'player_'.$type, $playerUnit);
                if ($type == 'hero' && count($data['omicron_abilities']) > 0) {
                    $this->_dataHelper->fillHeroOmicronAbility(
                        $playerUnit,
                        $data['omicron_abilities'],
                        $data['ability_data']
                    );
                }
                $this->_entityManagerInterface->flush();
            }
        } catch (Exception $e) {
            $arrayReturn['error_message'] = $e->getMessage();
            $arrayReturn['error_code'] = $e->getCode();
            return $arrayReturn;
        }
    }

    public function createPlayerShip(array $data, Player $player)
    {
        return $this->createPlayerUnit($data, $player, 'ship');
    }

    public function createPlayerHero(array $data, Player $player)
    {
        return $this->createPlayerUnit($data, $player, 'hero');
    }

    public function getNumberUnit($type)
    {
        $numberHeroes = $this->entityManagerInterface
            ->getRepository($type)
            ->findAll();
        return count($numberHeroes);
    }

    public function getPlayerUnitInformation(Player $player,string $type, $unit)
    {
        $arrayReturn = array();
        switch ($type) {
            case 'hero':
                $repo = $this->heroPlayerRepository;
            break;
            default:
                $repo = $this->shipPlayerRepository;
            break;
        }

        $unitInformations = $repo->getPlayerInformations($unit, $player);
        if (!empty($unitInformations)) {
            $unitInformation = $unitInformations[0];
            $arrayReturn['name'] = $unit->getName();
            $arrayReturn['rarity'] = $unitInformation->getNumberStars();
            $arrayReturn['level'] = $unitInformation->getLevel();
            $arrayReturn['gear_level'] = $unitInformation->getGearLevel();
            $arrayReturn['relic_level'] = $unitInformation->getRelicLevel();
            $arrayReturn['speed'] = $unitInformation->getSpeed();
            $arrayReturn['life'] = $unitInformation->getLife();
            $arrayReturn['protection'] = $unitInformation->getProtection();
            if ($type == 'hero') {
                $twOmicrons = $this->heroPlayerAbilityRepository->getTwOmicron($unitInformation);
            }
            if (!empty($twOmicrons)) {
                $arrayReturn['omicrons'] = array();
                foreach($twOmicrons as $omicron) {
                    array_push($arrayReturn['omicrons'],$omicron->getAbility()->getName());
                }
            }
        } else {
            $arrayReturn['name'] = 0;
            $arrayReturn['rarity'] = 0;
            $arrayReturn['level'] = 0;
            $arrayReturn['gear_level'] = 0;
            $arrayReturn['relic_level'] = 0;
            $arrayReturn['speed'] = 0;
            $arrayReturn['life'] = 0;
            $arrayReturn['protection'] = 0;
        }
        return $arrayReturn;
    }
}