<?php

namespace App\Utils\Manager;

use Exception;
use ReflectionClass;
use App\Entity\Unit as UnitEntity;
use App\Entity\Guild as GuildEntity;
use App\Entity\Squad as SquadEntity;
use App\Entity\Player as PlayerEntity;
use App\Repository\UnitPlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Entity\UnitPlayer as UnitPlayerEntity;
use App\Repository\HeroPlayerAbilityRepository;
use Symfony\Component\Console\Output\OutputInterface;
use App\Utils\Factory\UnitPlayer as UnitPlayerFactory;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Utils\Manager\HeroPlayerAbility as HeroPlayerAbilityManager;

class UnitPlayer extends BaseManager
{

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private HeroPlayerAbilityManager $heroPlayerAbilityManager,
        private UnitPlayerFactory $unitPlayerFactory,
        private UnitPlayerRepository $unitPlayerRepository,
        private HeroPlayerAbilityRepository $heroPlayerAbilityRepository,
        private TranslatorInterface $translator,
    ) {
        parent::__construct($entityManagerInterface);
    }

    public function getGuildPlayerUnitBySquad(GuildEntity $guild, SquadEntity $squad): array
    {
        $guildPlayersUnits = [];
        foreach ($squad->getUnits() as $squadUnit) {
            $unit = $squadUnit->getUnit();
            foreach ($guild->getPlayers() as $player) {
                $guildPlayersUnits[$player->getName()][$unit->getName()] = $this->getPlayerUnitByPlayerAndUnit(
                    $player,
                    $unit
                );
            }
        }
        return $guildPlayersUnits;
    }

    public function getPlayerUnitByPlayerAndUnit(Player $player, UnitEntity $unit): array
    {
        
        $unitPlayerData = $this->unitPlayerRepository->findOneBy(
            [
                'unit' => $unit,
                'player' => $player
            ]
        );

        return $this->fillExtractPlayerUnit($unitPlayerData);
    }

    private function fillExtractPlayerUnit(UnitPlayerEntity $unitPlayer = null): array
    {
        if (!empty($unitPlayer)) {
            $unitPlayerClassName = (new ReflectionClass($unitPlayer->getUnit()))->getShortName();
            $unitPlayerData = [
                'rarity' => $unitPlayer->getNumberStars(),
                'level' => $unitPlayer->getLevel(),
                'speed' => $unitPlayer->getSpeed(),
                'life' => $unitPlayer->getLife(),
                'protection' => $unitPlayer->getProtection()
            ];
            if ($unitPlayerClassName === 'Hero') {
                $unitPlayerData['gear_level'] = $unitPlayer->getGearLevel();
                $unitPlayerData['relic_level'] = $unitPlayer->getRelicLevel();
                $playerTwOmicrons = $this->getUnitPlayerOmicron($unitPlayer);
                if (!empty($playerTwOmicrons) && is_array($playerTwOmicrons)) {
                    foreach ($playerTwOmicrons as $omicron) {
                        $unitPlayerData['omicrons'][] = $this->translator->trans(
                            $omicron->getAbility()->getName(),
                            [],
                            'ability'
                        );
                    }
                }
            }
            return $unitPlayerData;
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

    public function getUnitPlayerOmicron(HeroPlayerEntity $heroPlayer): mixed
    {
        return $this->heroPlayerAbilityRepository
            ->getTwOmicron($heroPlayer);
    }

    public function updatePlayerUnits(
        PlayerEntity $player,
        array $dataPlayer,
        EntityManagerInterface $entityManagerInterface,
        OutputInterface $outputInterface = null
    ): void {
        if (
            !isset($dataPlayer['units']) ||
            !is_array($dataPlayer['units'])
        ) {
            throw new \Exception('Une erreur est survenue lors de la synchronisation des unités du joueur '.$player->getName(). '. Une modification de l\'API a du être faite');
        }

        if (!empty($outputInterface)) {
            $outputInterface->writeln(
                [
                    '<fg=green> Début de la synchronisation des unités',
                    '===========================</>',
                ]
            );
        }

        foreach ($dataPlayer['units'] as $unit) {
            if (
                !is_array($unit) ||
                !isset($unit['data'])
            ) {
                throw new \Exception('Une erreur est survenue lors de la synchronisation des unités du joueur '.$player->getName(). '. Une modification de l\'API a du être faite');
            }

            $unitPlayer = $this->unitPlayerFactory->getEntityByApiResponse($unit, $player, $entityManagerInterface);
            if (
                is_array($unit['data']['omicron_abilities']) &&
                count($unit['data']['omicron_abilities']) > 0
            ) {
                $this->heroPlayerAbilityManager->setHeroPlayerOmicrons($unitPlayer, $unit['data'], $entityManagerInterface);
            }
        }
        if (!empty($outputInterface)) {
            $outputInterface->writeln(
                [
                    '<fg=green>Synchronisation des données du joueur '.$player->getName(). ' : terminée',
                    '===========================</>',
                ]
            );
        }
    }
}