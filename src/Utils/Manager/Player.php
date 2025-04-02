<?php

namespace App\Utils\Manager;

use ReflectionClass;

use App\Dto\Api\Player as PlayerDto;
use App\Entity\Guild;
use App\Mapper\Player as PlayerMapper;
use App\Utils\Service\Api\SwgohGg;
use App\Utils\Manager\UnitPlayer as UnitPlayerManager;
use App\Repository\PlayerRepository;
use App\Entity\Player as PlayerEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Player extends BaseManager
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg,
        private UnitPlayerManager $unitPlayerManager,
        private PlayerRepository $playerRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validatorInterface
    ) 
    {
        parent::__construct($entityManagerInterface);
    }

    /**
     * @return bool|array<string, string>
     */
    public function updatePlayerWithApi(string $allyCode, Guild $guild) :array|bool
    {
        $count = 0;
        $playerData = $this->swgohGg->fetchPlayer($allyCode);
        if (
            isset($playerData['error_message_api_swgoh']) &&
            is_string($playerData['error_message_api_swgoh'])
        ) {
            return $playerData;
        }

        $player = $this->playerRepository->findOneBy(['id_swgoh' => $allyCode]);
        if (empty($player)) {
            $player = new PlayerEntity();
            $this->playerRepository->save($player);
        }

        $playerDto = new PlayerDto($playerData);
        $errors = $this->validatorInterface->validate($playerDto);
        if (count($errors) === 0) {
            $player = PlayerMapper::FromDTO($player, $playerDto, $guild);
            if (is_array($playerData['units'])) {
                $resultActionsPlayerUnits = $this->unitPlayerManager->updateUnitsPlayer($player, $playerData['units']);
                $this->playerRepository->save($player, true);
                return true;
            }
        }
        return ['error_message' => 'Erreur lors de la synchronisation des informations du joueur. Une modification de l\'API a du être faite'];
    }

    /**
     * @return string[]|array<string,array<string,mixed>>
     */
    public function getPlayerDataApi(PlayerEntity $player): array
    {
        $arrayReturn = [];
        $arrayReturn = $this->serializer->normalize(
            $player,
            null,
            [
                'groups' => [
                    'api_player'
                ]
            ]
        );
        return array_merge($arrayReturn, $this->getPlayerUnits($player));
    }

    public function getPlayerHeroesApi(PlayerEntity $player): array
    {
        return $this->getPlayerUnits($player, 'heroes')['heroes'];
    }

    public function getPlayerShipsApi(PlayerEntity $player): array
    {
        return $this->getPlayerUnits($player, 'ships')['ships'];
    }

    /**
     * @return string[]|array<string,array<string,mixed>>
     */
    public function getPlayerUnits(PlayerEntity $player, string $type = null): array
    {
        $arrayReturn = [];
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
                    $this->serializer->normalize(
                        $unit,
                        null,
                        [
                            'groups' => [
                                'api_player_unit'
                            ]
                        ]
                    ), $this->serializer->normalize(
                        $unit->getUnit(),
                        null,
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

    /**
     * @param array<string,array<string, mixed>> $dataGuild
     * @return string[]|array<string, string>|bool
     */
    public function updateGuildPlayers(Guild $guild, array $dataGuild): array|bool
    {
        $actualMembers = [];
        $playerNotSync = ['error_messages' => []];
        foreach ($dataGuild['data']['members'] as $key => $guildPlayerData) {
            if (
                is_array($guildPlayerData) &&
                isset($guildPlayerData['player_name']) && 
                isset($guildPlayerData['ally_code']) &&
                is_string($guildPlayerData['player_name']) &&
                is_string($guildPlayerData['ally_code'])
            ) {
                array_push($actualMembers, $guildPlayerData['player_name']);
                $result = $this->updatePlayerWithApi($guildPlayerData['ally_code'], $guild);
                if (
                    is_array($result) &&
                    isset($result['error_message']) &&
                    is_string($result['error_message'])
                ) {
                    array_push($playerNotSync['error_messages'], $result['error_message']);
                }
            } else {
                array_push(
                    $playerNotSync['error_messages'],
                    'Une erreur est survenue lors de la synchronisation du joueur numéro '.$key
                );
            }
        }

        if (!empty($playerNotSync['error_messages'])) {
            return $playerNotSync;
        }
        return $actualMembers;
    }
}