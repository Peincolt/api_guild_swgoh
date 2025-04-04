<?php

namespace App\Utils\Manager;

use Exception;
use App\Entity\Unit as UnitEntity;
use ReflectionClass;
use App\Entity\Guild as GuildEntity;
use App\Entity\Squad as SquadEntity;
use App\Entity\Player as PlayerEntity;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Repository\UnitRepository;
use App\Repository\UnitPlayerRepository;
use App\Utils\Factory\Unit as UnitFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\HeroPlayerAbilityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class UnitPlayer extends BaseManager
{

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository,
        private UnitFactory $unitFactory,
        private UnitRepository $unitRepository,
        private UnitPlayerRepository $unitPlayerRepository,
        private TranslatorInterface $translator 
    ) {
        parent::__construct($entityManagerInterface);
    }

    public function getGuildPlayerUnitBySquad(GuildEntity $guild, SquadEntity $squad)
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

    public function getPlayerUnitByPlayerAndUnit(Player $player, UnitEntity $unit)
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

    public function getUnitPlayerOmicron(HeroPlayerEntity $heroPlayer)
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

    /**
     * @return array<string,string>|bool
     */
    public function updateUnitsPlayer(PlayerEntity $player, array $dataPlayerUnits): array|bool
    {
        foreach ($playerData['units'] as $unit) {
            if (is_array($unit)) {
                $result = null;
                if (is_array($unit['data'])) {
                    $unit = $this->unitFactory->getEntityByApiResponse($unit['data'], $player);
                    if (!is_array($unit)) {
                        $this->unitRepository->save($unit);
                    } else {
                        return $unit;
                    }
                }
            } else {
                return [
                    'error_message' => 'Erreur lors de la synchronisation des unités du joueur. Une modification de l\'API a du être faite'
                ];
            }
        }
        return true;
    }
}