<?php

namespace App\Utils\Manager;

use ReflectionClass;

use App\Entity\Guild as GuildEntity;
use App\Utils\Service\Api\SwgohGg;
use App\Dto\Api\Player as PlayerDto;
use App\Repository\PlayerRepository;
use App\Entity\Player as PlayerEntity;
use App\Mapper\Player as PlayerMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Player extends BaseManager
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        private SwgohGg $swgohGg,
        private SerializerInterface $serializer,
        private ValidatorInterface $validatorInterface
    ) 
    {
        parent::__construct($entityManagerInterface);
    }

    public function updateGuildPlayer(
        GuildEntity $guild,
        array $arrayDataMember,
        EntityManagerInterface $entityManagerInterface,
        OutputInterface $outputInterface = null
    ): array {
        if (!isset($arrayDataMember['ally_code'])) {
            throw new \Exception('Une erreur est survenue lors de la synchronisation des joueurs de la guilde. Une modification de l\'API a du être faite');
        }

        $playerData = $this->swgohGg->fetchPlayer($arrayDataMember['ally_code']);
        if (isset($playerData['error_message_api_swgoh'])) {
            throw new \Exception($playerData['error_message_api_swgoh']);
        }

        $player = $entityManagerInterface->getRepository(PlayerEntity::class)
            ->findOneBy(['id_swgoh' => $arrayDataMember['ally_code']]);

        if (empty($player)) {
            $player = new PlayerEntity();
            $entityManagerInterface->persist($player);
        }

        $playerDto = new PlayerDto($playerData);
        $errors = $this->validatorInterface->validate($playerDto);
        if (count($errors) > 0) {
            throw new \Exception('Erreur lors de la synchronisation des informations du joueur. Une modification de l\'API a du être faite');
        }

        if (!empty($outputInterface)) {
            $outputInterface->writeln(
                [
                    '<fg=green>Synchronisation des données du joueur '.$playerDto->name. ' : terminée',
                    '===========================</>',
                ]
            );
        }

        return [PlayerMapper::FromDTO($player, $playerDto, $guild), $playerData];
    }

    /**
     * @return string[]|array<string,array<string,mixed>>
     */
    public function getPlayerDataApi(PlayerEntity $player): array
    {
        $playerData = $this->serializer->normalize(
            $player,
            null,
            [
                'groups' => [
                    'api_player'
                ]
            ]
        );

        if (!is_array($playerData)) {
            return [];   
        }

        return array_merge($playerData, $this->getPlayerUnits($player));
    }

    public function getPlayerHeroesApi(PlayerEntity $player): array
    {
        $playerHeroes = $this->getPlayerUnits($player, 'heroes')['heroes'];
        return $playerHeroes['heroes'] ?? [];
    }

    public function getPlayerShipsApi(PlayerEntity $player): array
    {
        $playerShips = $this->getPlayerUnits($player, 'ships')['ships'];
        return $playerShips['ships'] ?? [];
    }

    /**
     * @return string[]|array<string,array<string,mixed>>
     */
    public function getPlayerUnits(PlayerEntity $player, string $unitType = null): array
    {
        $playerUnits = [];
        $playerUnitsCollection = $player->getUnitPlayers();
        foreach ($playerUnitsCollection as $playerUnit) {
            $className = (new \ReflectionClass($playerUnit))->getShortName();
            $unitTypeName = match ($className) {
                'HeroPlayer' => 'heroes',
                'ShipPlayer' => 'ships',
                default => null
            };

            if (empty($unitType) || $unitType == $unitTypeName) {
                $arrayUnitTypeData = $this->serializer->normalize(
                    $playerUnit,
                    null,
                    [
                        'groups' => [
                            'api_player_unit'
                        ]
                    ]
                );
                
                $arrayUnitData = $this->serializer->normalize(
                    $playerUnit->getUnit(),
                    null,
                    [
                        'groups' => [
                            'api_player_unit'
                        ]
                    ]
                );

                if (
                    !is_array($arrayUnitTypeData) ||
                    !is_array($arrayUnitData)
                ) {
                    continue;
                }

                $playerUnits[$unitTypeName][] = array_merge($arrayUnitTypeData, $arrayUnitData);
            }
        }
        return $playerUnits;
    }
}