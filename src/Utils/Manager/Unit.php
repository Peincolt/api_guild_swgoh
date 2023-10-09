<?php

namespace App\Utils\Manager;

use App\Entity\Hero;
use App\Entity\Unit as UnitEntity;
use App\Utils\Service\Api\SwgohGg;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;

class Unit
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg,
    )
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function updateUnit(string $type) :bool|array
    {
        $entityName = "\App\Entity\\".$type;
        $data = $this->swgohGg->fetchHeroOrShip($type);
        if (!isset($data['error_message'])) {
            $count = 0;
            foreach ($data as $key => $value) {
                $count++;
                $unit = $this->entityManagerInterface
                    ->getRepository(UnitEntity::class)
                    ->findOneBy(
                        [
                            'base_id' => $data[$key]['base_id']
                        ]
                    );
                if (empty($unit)) {
                    $unit = new $entityName;
                    $this->entityManagerInterface->persist($unit);
                }
                $this->fillUnit($unit, $data[$key]);
                if ($count > 1000) {
                    $count = 0;
                    $this->entityManagerInterface->flush();
                }
            }
            $this->entityManagerInterface->flush();
            return true;
        }
        return $data;
    }

    public function fillUnit(UnitEntity $unit, array $data) :UnitEntity
    {
        $reflection = new ReflectionClass($unit);
        $className = $reflection->getShortName();
        $unit->setBaseId($data['base_id']);
        $unit->setName($data['name']);
        $unit->setImage($data['image']);
        $unit->setCategories($data['categories']);
        if ($className == 'Hero') {
            $unit->setIdSwgoh($data['pk']);
        }
        return $unit;
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