<?php

namespace App\Utils\Manager;

use Exception;
use App\Entity\Unit;
use App\Utils\Service\Api\SwgohGg;
use App\Repository\GuildRepository;
use App\Entity\Guild as GuildEntity;
use App\Repository\PlayerRepository;
use App\Repository\SquadRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Player as playerManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Guild
{
    public function __construct(
        private SwgohGg $swgohGg,
        private EntityManagerInterface $entityManagerInterface,
        private GuildRepository $guildRepository,
        private PlayerManager $playerManager,
        private PlayerRepository $playerRepository,
        private SerializerInterface $serializer
    )
    {}

    public function updateGuild(string $idGuild,OutputInterface $outputInterface = null)
    {
        $actualMembers = array();
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
            array_push($actualMembers, $guildPlayerData['player_name']);
            $result = $this->playerManager
                ->updatePlayerWithApi($guildPlayerData['ally_code'], $guild);
            if (is_array($result)) {
                return $result;
            }
        }
        $this->deleteOlderMembers($actualMembers, $guild);
        $this->guildRepository->save($guild, true);
    }

    public function fillGuild(GuildEntity $guild, array $data) :GuildEntity
    {
        $guild->setName($data['name']);
        $guild->setNumberPlayers($data['member_count']);
        $guild->setIdSwgoh($data['guild_id']);
        $guild->setGalacticPower($data['galactic_power']);
        return $guild;
    }

    public function getGuildDataApi(GuildEntity $guild)
    {
        $arrayReturn = array();
        $arrayReturn = $this->serializer->normalize($guild, null, ['groups' => ['api_guild']]);
        foreach ($guild->getPlayers() as $player) {
            $arrayReturn['players'][] = $this->playerManager
                ->getPlayerDataApi($player);
        }
        return $arrayReturn;
    }

    private function deleteOlderMembers(array $actualMembers, GuildEntity $guild)
    {
        $allMembers = $guild->getPlayers();
        foreach($allMembers as $member) {
            if (!in_array($member->getName(), $actualMembers)) {
                $this->playerRepository->remove($member);
            }
        }
        $this->entityManagerInterface->flush();
    }
}