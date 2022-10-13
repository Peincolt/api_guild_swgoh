<?php

namespace App\Utils\Manager;

use App\Entity\Guild;
use App\Entity\Player as PlayerEntity;
use App\Repository\PlayerRepository;
use App\Utils\Service\Api\SwgohGg;
use App\Utils\Manager\HeroPlayer as HeroPlayerManager;
use App\Utils\Manager\ShipPlayer as ShipPlayerManager;
use Doctrine\ORM\EntityManagerInterface;

class Player
{
    public function __construct(
        private SwgohGg $swgohGg, 
        private HeroPlayerManager $heroPlayerManager, 
        private ShipPlayerManager $shipPlayerManager,
        private PlayerRepository $playerRepository
    ) 
    {}

    public function updatePlayerWithApi(string $allyCode, Guild $guild)
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

    public function updatePlayerByApi(int $allyCode, bool $characters = false, bool $ships = false)
    {
        $playerDatas = $this->_swgoh->fetchPlayer($allyCode);
        if (is_array($playerDatas)) {
            $this->updatePlayer($playerDatas, $characters, $ships);
            return true;
        }
        return false;
    }

    public function getFields()
    {
        $arrayReturn = array();
        $arrayReturn['name'] = 'Nom';
        $arrayReturn['galactical_puissance'] = 'Puissance galactique';
        $arrayReturn['characters_galactical_puissance'] = 'Puissance galactique des héros';
        $arrayReturn['ships_galactical_puissance'] = 'Puissance galactique des vaisseaux';
        return $arrayReturn;
    }
}