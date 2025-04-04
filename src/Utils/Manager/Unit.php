<?php

namespace App\Utils\Manager;

use App\Entity\Hero;
use App\Entity\Unit as UnitEntity;
use App\Utils\Factory\Unit as UnitFactory;
use App\Utils\Service\Api\SwgohGg;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;

class Unit
{
    private $entityManagerInterface;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg,
        private UnitFactory $unitFactory
    )
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function updateUnit(string $type) :bool|array
    {
        $dataUnits = $this->swgohGg->fetchHeroOrShip($type);
        if (!isset($dataUnits['error_message_api_swgoh'])) {
            $this->entityManagerInterface->beginTransaction();
            try {
                foreach ($dataUnits as $key => $dataUnit) {
                    $unit = $this->unitFactory->getEntityByApiResponse($dataUnit, $type);
                    if (!is_array($unit)) {
                        $this->entityManagerInterface->persist($unit);
                    } else {
                        throw new \Exception($unit['error_message']);
                    }
                }
                $this->entityManagerInterface->flush();
                $this->entityManagerInterface->commit();
                return true;
            } catch(\Exception $e) {
                $this->entityManagerInterface->rollback();
                return [
                    'error_message' => $e->getMessage()
                ];
            }
        }
        return $dataUnits;
    }

    public function getEntityByRoute(string $route)
    {
        $arrayReturn = array();
        $array = explode('_', $route);
        if (count($array) > 2) {
            $arrayReturn['name'] = ucfirst($array[2]);
        } else {
            $arrayReturn['name'] = ucfirst($array[1]);
        }
        $arrayReturn['namespace'] = "App\Entity\\".$arrayReturn['name'];
        $arrayReturn['player_class_name'] = $arrayReturn['name'].'Player';
        $arrayReturn['player_namespace_class'] = "App\Entity\\".$arrayReturn['player_class_name'];
        $arrayReturn['function'] = 'get'.$arrayReturn['player_class_name'].'s';
        return $arrayReturn;
    }

    public function getUnits($type)
    {
        $arrayReturn = array();
        $i=0;
        $units = $this->entityManagerInterface
            ->getRepository("App\Entity\\".ucfirst($type))
            ->findAll();
        foreach ($units as $unit) {
            $arrayReturn[$unit->getName()] = $unit->getId();
        }

        return $arrayReturn;
    }

    public function getAllUnits()
    {
        //$item->expiresAfter(3600);
        
        $arrayReturn = array();

        $heroes = $this->entityManagerInterface
            ->getRepository("App\Entity\Hero")
            ->findAll();
        $ships = $this->entityManagerInterface
            ->getRepository("App\Entity\Ship")
            ->findAll();

        foreach ($heroes as $hero) {
            array_push($arrayReturn, htmlentities($hero->getName()));
        }

        foreach ($ships as $ship) {
            array_push($arrayReturn, htmlentities($ship->getName()));
        }

        return $arrayReturn;
        
    }

    public function findUnitByName($name) {
        $arrayReturn = array();
        $name = html_entity_decode($name);

        $hero = $this->entityManagerInterface
            ->getRepository("App\Entity\Hero")
            ->findOneBy(['name' => $name]);
        $arrayReturn['type'] = 'Hero';
        $arrayReturn['data'] = $hero;

        if (empty($hero)) {
            $ship = $this->entityManagerInterface
                ->getRepository("App\Entity\Ship")
                ->findOneBy(['name' => $name]);
            $arrayReturn['type'] = 'Ship';
            $arrayReturn['data'] = $ship;
        }

        return $arrayReturn;

    }

    public function getFrontUnitInformation($route,$id)
    {
        $arrayReturn = array();
        $entityInformation = $this->getEntityByRoute($route);
        $unit = $this->entityManagerInterface
            ->getRepository($entityInformation['namespace'])
            ->find($id);
        
        if (!empty($unit)) {
            $arrayReturn['name'] = $unit->getName();
            $functionName = $entityInformation['function'];
            $unitPlayers = $unit->$functionName();
    
            for($i=0;$i<count($unitPlayers);$i++) {
                $arrayReturn['players'][$i]['player_name'] = $unitPlayers[$i]->getPlayer()->getName();
                $arrayReturn['players'][$i]['level'] = $unitPlayers[$i]->getLevel();
                $arrayReturn['players'][$i]['stars'] = $unitPlayers[$i]->getNumberStars();
                $arrayReturn['players'][$i]['galactical_puissance'] = $unitPlayers[$i]->getGalacticalPuissance();
                if ($entityInformation['name'] == 'Hero') {
                    $arrayReturn['players'][$i]['relic'] = $unitPlayers[$i]->getRelicLevel();
                    $arrayReturn['players'][$i]['gear_level'] = $unitPlayers[$i]->getGearLevel();
                }
            }
        } else {
            $arrayReturn['error_message'] = 'Impossible de trouver l\'unité que vous cherchez';
        }

        return $arrayReturn;
    }

    public function setFields($type)
    {
        $arrayReturn = array();

        switch ($type) {
            case 'Hero':
                $arrayReturn['hero.name'] = 'nom';
                $arrayReturn['number_stars'] = 'Nom d\'étoile';
                $arrayReturn['level'] = 'Niveau';
                $arrayReturn['gear_level'] = 'Niveau d\'équipement';
                $arrayReturn['relic_level'] = 'Niveau de relique';
                $arrayReturn['galactical_puissance'] = 'Puissance galactique';
            break;

            case 'Ship':
                $arrayReturn['ship.name'] = 'nom';
                $arrayReturn['number_stars'] = 'Nom d\'étoile';
                $arrayReturn['level'] = 'Niveau';
                $arrayReturn['galactical_puissance'] = 'Puissance galactique';
            break;
        }

        return $arrayReturn;
    }
}