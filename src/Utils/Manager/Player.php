<?php

namespace App\Utils\Manager;

use App\Entity\Guild;
use App\Entity\Player as PlayerEntity;
use App\Repository\PlayerRepository;
use App\Utils\Service\Api\SwgohGg;
use App\Utils\Manager\HeroPlayer as HeroPlayerManager;
use App\Utils\Manager\ShipPlayer as ShipPlayerManager;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;

class Player extends BaseManager
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg, 
        private HeroPlayerManager $heroPlayerManager, 
        private ShipPlayerManager $shipPlayerManager,
        private PlayerRepository $playerRepository,
        private SerializerInterface $serializer
    ) 
    {
        parent::__construct($entityManagerInterface);
    }

    public function updatePlayerWithApi(string $allyCode, Guild $guild) :bool|array
    {
        $count = 0;
        $playerData = $this->swgohGg->fetchPlayer($allyCode);
        if (isset($playerData['error_message'])) {
            return $playerData;
        }

        $player = $this->playerRepository->findOneBy(['id_swgoh' => $allyCode]);
        if (empty($player)) {
            $player = new PlayerEntity();
            $this->playerRepository->save($player);
        }

        $player = $this->fillPlayer($player, $playerData, $guild);
        foreach ($playerData['units'] as $unit) {
            $count++;
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
        $this->playerRepository->save($player, true);
        return true;
    }

    public function fillPlayer(PlayerEntity $player, array $data, Guild $guild) :PlayerEntity
    {
        if (preg_match("#^[0-9]+$#", $data['data']['last_updated'])) {
            $date = new \DateTime();
            $date->setTimestamp($data['data']['last_updated']);
        } else {
            $dateCreation = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                preg_replace("#[a-zA-Z]+#", ' ', $data['data']['last_updated'])
            );
            $date = new \DateTime(
                (
                    \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        preg_replace(
                            "#[a-zA-Z]+#", ' ', $data['data']['last_updated']
                        )
                    )
                )->format('Y-m-d H:i')
            );
        }
        $player->setGuild($guild);
        $player->setLastUpdate($date);
        $player->setIdSwgoh($data['data']['ally_code']);
        $player->setName($data['data']['name']);
        $player->setLevel($data['data']['level']);
        $player->setGalacticalPower($data['data']['galactic_power']);
        $player->setHeroesGelacticPower($data['data']['character_galactic_power']);
        $player->setShipsGalacticPower($data['data']['ship_galactic_power']);
        $player->setGearGiven($data['data']['guild_exchange_donations']);
        return $player;
    }

    public function updatePlayerGuild(Guild $guild, array $arrayDataPlayer, bool $characters = false, bool $ships = false)
    {
        $player = $this->updatePlayer($arrayDataPlayer, $characters, $ships);
        $player->setGuild($guild);
        $this->_entityManager->persist($player);
        $this->_entityManager->flush();
    }

    public function getPlayerDataApi(PlayerEntity $player)
    {
        $arrayReturn = array();
        $arrayReturn['data'] = $this->serializer->normalize($player, null, ['groups' => ['api_player']]);
        return array_merge($arrayReturn, $this->getPlayerUnits($player));
    }

    public function getPlayerHeroesApi(PlayerEntity $player)
    {
        return $this->getPlayerUnits($player, 'heroes');
    }

    public function getPlayerShipsApi(PlayerEntity $player)
    {
        return $this->getPlayerUnits($player, 'ships');
    }

    public function getPlayerUnits(PlayerEntity $player, string $type = null)
    {
        $arrayReturn = array();
        $units = $player->getUnitPlayers();
        foreach ($units as $unit) {
            $classInformation = new ReflectionClass($unit);
            if ($classInformation->getShortName() == 'HeroPlayer') {
                $unitTypeName = 'heroes';
            } else {
                $unitTypeName = 'ships';
            }

            if (empty($type) || $type == $unitTypeName) {
                $unitInformation = array_merge(
                    $this->serializer->normalize($unit, null,
                        [
                            'groups' => [
                                'api_player_unit'
                            ]
                        ]
                    ), $this->serializer->normalize($unit->getUnit(), null,
                        [
                            'groups' => [
                                'api_player_unit'
                            ]
                        ]
                    )
                );
                $arrayReturn[$unitTypeName][] = $unitInformation;
            }
        }
        return $arrayReturn;
    }
}