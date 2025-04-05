<?php

namespace App\Utils\Manager;

use Exception;
use App\Dto\Api\Guild as GuildDto;
use App\Mapper\Guild as GuildMapper;
use App\Utils\Service\Api\SwgohGg;
use App\Entity\Guild as GuildEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Player as playerManager;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Guild
{
    public function __construct(
        private SwgohGg $swgohGg,
        private EntityManagerInterface $entityManagerInterface,
        private PlayerManager $playerManager,
        private UnitPlayerManager $unitPlayerManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validatorInterface
    ) {
    }

    public function updateAllGuild(
        string $idGuild,
        OutputInterface $outputInterface = null
    ) :mixed {
        $index = 0;
        $maxTransactionNumber = 10;
        $dataGuild = $this->swgohGg->fetchGuild($idGuild);
        if (is_array($dataGuild)) {
            $actualMembers = [];
            $this->entityManagerInterface->beginTransaction();
            try {
                $guild = $this->updateGuild($idGuild, $dataGuild, $this->entityManagerInterface, $outputInterface);
                foreach ($dataGuild['data']['members'] as $key => $guildPlayerData) {
                    [$playerEntity, $playerInformations] = $this->playerManager
                        ->updateGuildPlayer(
                            $guild,
                            $guildPlayerData,
                            $this->entityManagerInterface,
                            $outputInterface
                    );
                    array_push($actualMembers, $playerEntity->getName());
                    $this->unitPlayerManager->updatePlayerUnits($playerEntity, $playerInformations, $this->entityManagerInterface, $outputInterface);
                    if (($index % $maxTransactionNumber) === 10) {
                        $this->entityManagerInterface->flush();
                        $this->entityManagerInterface->clear();
                    }
                    $index++;
                }

                if (!empty($actualMembers)) {
                    $this->deleteOlderMembers($actualMembers, $guild);
                }
                $this->entityManagerInterface->flush();
                $this->entityManagerInterface->commit();
            } catch (\Exception $e) {
                $this->entityManagerInterface->rollback();
                return [
                    'error_message' => $e->getMessage()
                ];
            }
            return $dataGuild;
        }
    }

    public function updateGuild(
        string $idGuild,
        array $dataGuild,
        EntityManagerInterface $entityManagerInterface,
        OutputInterface $outputInterface = null
    ) :GuildEntity {
        if (isset($dataGuild['error_message_api_swgoh'])) {
            throw new \Exception($dataGuild['error_message_api_swgoh']);
        }

        if (!isset($dataGuild['data']) || !is_array($dataGuild['data'])) {
            throw new \Exception('Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite');
        }

        $guildDto = new GuildDto($dataGuild);
        $errors = $this->validatorInterface->validate($guildDto);
        if (count($errors) > 0) {
            throw new \Exception('Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite');
        }

        $guild = $entityManagerInterface->getRepository(GuildEntity::class)->findOneBy(
            [
                'id_swgoh' => $guildDto->id_swgoh
            ]
        );

        if (empty($guild)) {
            $guild = new GuildEntity();
            $entityManagerInterface->persist($guild);
        }

        $guild = GuildMapper::fromDTO($guild, $guildDto);
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
        return $guild;
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

        if (!is_array($arrayReturn)) {
            return [];
        }

        $arrayReturn['players'] = [];

        foreach ($guild->getPlayers() as $player) {
            $arrayReturn['players'][] = $this->playerManager
                ->getPlayerDataApi($player);
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
                $this->entityManagerInterface->remove($member);
            }
        }
    }
}