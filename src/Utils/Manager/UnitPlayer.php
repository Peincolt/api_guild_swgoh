<?php

namespace App\Utils\Manager;

use Exception;
use App\Entity\Unit;
use ReflectionClass;
use App\Entity\Guild;
use App\Entity\Squad;
use App\Entity\Player;
use App\Entity\HeroPlayer;
use App\Repository\UnitRepository;
use Symfony\Component\Serializer\v;
use App\Repository\HeroPlayerRepository;
use App\Repository\ShipPlayerRepository;
use App\Repository\UnitPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\HeroPlayerAbilityRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UnitPlayer extends BaseManager
{
    protected $normalizers;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private HeroPlayerRepository $heroPlayerRepository,
        private ShipPlayerRepository $shipPlayerRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository,
        private UnitRepository $unitRepository,
        private UnitPlayerRepository $unitPlayerRepository,
        private SerializerInterface $serializer,
        private TranslatorInterface $translator 
    ) {
        parent::__construct($entityManagerInterface);
    }

    public function getGuildPlayerUnitBySquad(Guild $guild, Squad $squad)
    {
        $arrayData = array();
        foreach ($squad->getUnits() as $squadUnit) {
            $unit = $squadUnit->getUnit();
            foreach ($guild->getPlayers() as $player) {
                $arrayData[$player->getName()][$unit->getName()] = $this->getPlayerUnitByPlayerAndUnit(
                    $player,
                    $unit
                );
            }
        }
        return $arrayData;
    }

    public function getPlayerUnitByPlayerAndUnit(Player $player, Unit $unit)
    {
        
        $unitPlayerData = $this->unitPlayerRepository->findOneBy(
            [
                'unit' => $unit,
                'player' => $player
            ]
        );

        return $this->fillExtractPlayerUnit($unitPlayerData);
    }

    private function fillExtractPlayerUnit(UnitPlayerEntity $unitPlayer = null)
    {
        if (!empty($unitPlayer)) {
            $heroReflectionClass = new ReflectionClass($unitPlayer->getUnit());
            $arrayPlayerData = [
                'rarity' => $unitPlayer->getNumberStars(),
                'level' => $unitPlayer->getLevel(),
                'speed' => $unitPlayer->getSpeed(),
                'life' => $unitPlayer->getLife(),
                'protection' => $unitPlayer->getProtection()
            ];
            if ($heroReflectionClass->getShortName() == 'Hero') {
                $arrayPlayerData['gear_level'] = $unitPlayer->getGearLevel();
                $arrayPlayerData['relic_level'] = $unitPlayer->getRelicLevel();
                $playerTwOmicrons = $this->getUnitPlayerOmicron($unitPlayer);
                if (!empty($playerTwOmicrons)) {
                    foreach ($playerTwOmicrons as $omicron) {
                        $arrayPlayerData['omicrons'][] = $this->translator->trans(
                            $omicron->getAbility()->getName(),
                            [],
                            'ability'
                        );
                    }
                }
            }
            return $arrayPlayerData;
        }

        return array(
            'rarity' => 0,
            'level' => 0,
            'gear_level' => 0,
            'relic_level' => 0,
            'speed' => 0,
            'life' => 0,
            'protection' => 0        
        );
    }

    public function getUnitPlayerOmicron(HeroPlayer $heroPlayer)
    {
        return $this->heroPlayerAbilityRepository
            ->getTwOmicron($heroPlayer);
    }

    public function fillUnitPlayer(
        UnitPlayerEntity $unitPlayer,
        array $data
    ) :UnitPlayerEntity {
        $unitPlayer->setNumberStars($data['rarity']);
        $unitPlayer->setLevel($data['level']);
        $unitPlayer->setGalacticalPower($data['power']);
        $unitPlayer->setSpeed($data['stats']['5']);
        $unitPlayer->setLife($data['stats']['1']);
        $unitPlayer->setProtection(intval($data['stats']['28']));
        return $unitPlayer;
    }

    public function updateUnitsPlayer(Player $player, array $dataPlayerUnits): array|bool
    {
        foreach ($playerData['units'] as $unit) {
            if (is_array($unit)) {
                $result = null;
                if (is_array($unit['data'])) {
                    switch ($unit['data']['combat_type']) {
                        case 1:
                            $result = $this->heroPlayerManager->createHeroPlayer(
                                $player,
                                $unit['data'],
                            );
                        break;
                        case 2:
                            $result = $this->shipPlayerManager->createShiplayer(
                                $player,
                                $unit['data']
                            );
                        break;
                    }
    
                    if (is_array($result)) {
                        return $result;
                    }
                }
            }
        }
        return true;
    }
}