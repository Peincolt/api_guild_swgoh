<?php

namespace App\Utils\Manager;

use Exception;
use App\Utils\Service\Api\SwgohGg;
use App\Utils\Manager\Player as playerManager;
use App\Repository\GuildRepository;
use App\Entity\Guild as GuildEntity;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Guild
{
    public function __construct(
        private SwgohGg $swgohGg,
        private EntityManagerInterface $entityManagerInterface,
        private GuildRepository $guildRepository,
        private PlayerManager $playerManager,
        private PlayerRepository $playerRepository
    )
    {}

    public function updateGuild(string $idGuild,OutputInterface $outputInterface = null)
    {
        $arrayActualMembers = array();
        $dataGuild = $this->swgohGg->fetchGuild($idGuild);
        $count = 0;
        if (isset($dataGuild['error_message'])) {
            return $dataGuild;
        }

        $guild = $this->guildRepository->findOneBy(
            [
                'id_swgoh' => $dataGuild['data']['guild_id']
            ]
        );

        if (empty($guild)) {
            $guild = new GuildEntity();
        }

        $guild = $this->fillGuild($guild, $dataGuild['data']);
        $this->guildRepository->save($guild, true);

        if (!empty($outputInterface)) {
            $outputInterface->writeln(
                [
                    '<fg=green>Synchronisation des données de la guilde terminée',
                    '===========================</>',
                    '<fg=yellow>Début de la synchronisation des informations des joueurs de la guilde',
                    '===========================</>'
                ]
            );
        }

        foreach ($dataGuild['data']['members'] as $guildPlayerData) {
            array_push($arrayActualMembers, $guildPlayerData);
            $result = $this->playerManager
                ->updatePlayerWithApi($guildPlayerData['ally_code'], $guild);
            if (is_array($result)) {
                return $result;
            }
            /*if ($count == 10) {
                $this->entityManagerInterface->flush();
                $count = 0;
            } else {
                $count++;
            }*/
        }
        $this->guildRepository->save($guild, true);
    }

    public function updateGuildPlayers(array $dataGuild, bool $characters = false, bool $ships = false)
    {
        $arrayActualMembers = array();
        $guild = $this->_entityManagerInterface
            ->getRepository(EntityGuild::class)
            ->findOneBy(
                [
                    'id_swgoh' => $dataGuild['data']['guild_id']
                ]
            );
        foreach ($dataGuild['data']['members'] as $guildPlayerData) {
            array_push($arrayActualMembers, $guildPlayerData['player_name']);
            $playerData = $this->_swgohGg->fetchPlayer(
                $guildPlayerData['ally_code']
            );
            $this->_playerHelper->updatePlayerGuild(
                $guild,
                $playerData,
                $characters,
                $ships
            );
        }
        $playersOut = $this->_playerRepository->getOldMembers(
            $guild,
            $arrayActualMembers
        );
        foreach ($playersOut as $player) {
            $this->_entityManagerInterface->remove($player);
        }
        $this->_entityManagerInterface->flush();
        return 200;
    }

    public function fillGuild(GuildEntity $guild, array $data) :GuildEntity
    {
        $guild->setName($data['name']);
        $guild->setNumberPlayers($data['member_count']);
        $guild->setIdSwgoh($data['guild_id']);
        $guild->setGalacticPower($data['galactic_power']);
        return $guild;
    }

    public function getFormGuild()
    {
        $arrayReturn = array();
        $guilds = $this->_entityManagerInterface
            ->getRepository('App\Entity\Guild')
            ->findAll();

        foreach ($guilds as $guild) {
            $arrayReturn[$guild->getName()] = $guild->getId();
        }

        return $arrayReturn;
    }

    public function getHeroesGalacticalPower(EntityGuild $guild)
    {
        $galacticalPower = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $galacticalPower+= intval($player->getCharactersGalacticalPuissance());
        }
        return $galacticalPower;
    }

    public function getShipsGalacticalPower(EntityGuild $guild)
    {
        $galacticalPower = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $galacticalPower+= intval($player->getShipsGalacticalPuissance());
        }
        return $galacticalPower;
    }

    public function getHeroesNumber(EntityGuild $guild)
    {
        $heroesNumber = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $heroesNumber+= intval(count($player->getCharacters()));
        }
        return $heroesNumber;
    }

    public function getShipsNumber(EntityGuild $guild)
    {
        $shipsNumber = 0;
        $players = $guild->getPlayers();
        foreach ($players as $player) {
            $shipsNumber+= intval(count($player->getShips()));
        }
        return $shipsNumber;
    }


}