<?php

namespace App\Utils\Manager;

use Exception;
use App\Dto\Api\Guild as GuildDto;
use App\Entity\Unit;
use App\Mapper\Guild as GuildMapper;
use App\Utils\Service\Api\SwgohGg;
use App\Repository\GuildRepository;
use App\Repository\SquadRepository;
use App\Entity\Guild as GuildEntity;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Player as playerManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Guild
{
    public function __construct(
        private SwgohGg $swgohGg,
        private EntityManagerInterface $entityManagerInterface,
        private GuildRepository $guildRepository,
        private PlayerManager $playerManager,
        private PlayerRepository $playerRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validatorInterface
    ) {
    }

    /**
     *@return array<string, mixed>|bool
     */
    public function updateGuild(
        string $idGuild,
        OutputInterface $outputInterface = null
    ) :array|bool {
        $actualMembers = array();
        $dataGuild = $this->swgohGg->fetchGuild($idGuild);
        $count = 0;

        if (is_array($dataGuild)) {
            if (!isset($dataGuild['error_message'])) {
                if (
                    isset($dataGuild['data']) &&
                    is_array($dataGuild['data']) &&
                    isset($dataGuild['data']['guild_id']) &&
                    is_string($dataGuild['data']['guild_id'])
                ) {
                    $guildDto = new GuildDto($dataGuild);
                    $errors = $this->validatorInterface->validate($guildDto);
                    if (count($errors) === 0) {
                        $guild = $this->guildRepository->findOneBy(
                            [
                                'id_swgoh' => (string) $dataGuild['data']['guild_id']
                            ]
                        );
    
                        if (empty($guild)) {
                            $guild = new GuildEntity();
                        }

                        $guild = GuildMapper::fromDTO($guild, $guildDto);
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

                        if (
                            isset($dataGuild['data']['members']) &&
                            is_array($dataGuild['data']['members'])
                        ) {
                            foreach ($dataGuild['data']['members'] as $key => $guildPlayerData) {
                                $playerNotSync = ['error_messages' => []];
                                if (
                                    is_array($guildPlayerData) &&
                                    isset($guildPlayerData['player_name']) && 
                                    isset($guildPlayerData['ally_code']) &&
                                    is_string($guildPlayerData['player_name']) &&
                                    is_string($guildPlayerData['ally_code'])
                                ) {
                                    array_push($actualMembers, $guildPlayerData['player_name']);
                                    $result = $this->playerManager
                                        ->updatePlayerWithApi($guildPlayerData['ally_code'], $guild);
                                    if (is_array($result)) {
                                        return $result;
                                    }
                                } else {
                                    array_push(
                                        $playerNotSync['error_messages'],
                                        'Une erreur est survenue lors de la synchronisation du joueur numéro '.$key
                                    );
                                }
                            }
                            $this->deleteOlderMembers($actualMembers, $guild);
                            $this->guildRepository->save($guild, true);
                            return true;
                        }
                        return ['error_message' => 'Une erreur est survenue lors de la récupération des informations des joueurs de la guilde'];
                    }
                    return ['error_message' => 'Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite'];
                }
            }
            return ['error_message' => $dataGuild['error_message']];
        }
        return ['error_message' => 'Une erreur est survenue lors de la récupération des informations de la guilde via l\'API'];
    }

    public function getGuildDataApi(GuildEntity $guild): mixed
    {
        $arrayReturn = $this->serializer->normalize(
            $guild,
            null,
            [
                'groups' => [
                    'api_guild'
                ]
            ]
        );

        if (!empty($arrayReturn) && is_array($arrayReturn)) {
            if (!isset($arrayReturn['players'])) {
                $arrayReturn['players'] = [];
            }
            foreach ($guild->getPlayers() as $player) {
                $arrayReturn['players'][] = $this->playerManager
                    ->getPlayerDataApi($player);
            }
        }
        return $arrayReturn;
    }

    /**
     * @param string[] $actualMembers
     */
    private function deleteOlderMembers(array $actualMembers, GuildEntity $guild): void
    {
        $allMembers = $guild->getPlayers();
        foreach ($allMembers as $member) {
            if (!in_array($member->getName(), $actualMembers)) {
                $this->playerRepository->remove($member);
            }
        }
        $this->entityManagerInterface->flush();
    }
}