<?php

namespace App\Utils\Manager;

use App\Repository\GuildRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use App\Utils\Manager\Guild as GuildManager;
use App\Utils\Manager\Player as PlayerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GuildTest extends KernelTestCase
{
    private GuildRepository $mockGuildRepository;
    private PlayerManager $mockPlayerManager;
    private EntityManagerInterface $mockEntityManagerInterface;
    private SwgohGgApi $mockSwgogGgApi;
    private PlayerRepository $mockPlayerRepository;
    private ValidatorInterface $validatorInterface;
    private SerializerInterface $serializer;
    /**
     * @var string[]
     */
    private $baseSwgohggData;

    // On set up toutes les variables/mocks commun pour tous les tests
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->baseSwgohggData = [
            "data"=> [
                "guild_id"=> "uuwcpRBoStWfogZersAvJA",
                "name"=> "HGamers II",
                "external_message"=> "Guilde actif.Discord obligatoire(https://discord.gg/vZ8AeM3) naboo caisse 90M. BT ROTE 33*",
                "banner_color_id"=> "cyan_purple",
                "banner_logo_id"=> "guild_icon_blast",
                "enrollment_status"=> 2,
                "galactic_power"=> 526709498,
                "guild_type"=> null,
                "level_requirement"=> 85,
                "member_count"=> 49,
                "members" => [
                    [
                        "galactic_power"=> 11395612,
                        "guild_join_time"=> "2021-10-03T20:52:48Z",
                        "lifetime_season_score"=> 734060,
                        "member_level"=> 3,
                        "ally_code"=> 246639295,
                        "player_level"=> 85,
                        "player_name"=> "Wyøming",
                        "league_id"=> "CHROMIUM",
                        "league_name"=> "Chromium",
                        "league_frame_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_portrait_league_chromium.png",
                        "portrait_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_kalani.png",
                        "title"=> "Bitter Pill Company",
                        "squad_power"=> 176324
                    ]
                ]
            ]
        ];

        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->serializer = $container->get(SerializerInterface::class);
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockGuildRepository = $this->createMock(GuildRepository::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockSwgogGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockEntityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->mockPlayerManager = $this->createMock(PlayerManager::class);
        $this->mockEntityManagerInterface->method('persist')->willReturn(null);
        $this->mockEntityManagerInterface->method('flush')->willReturn(null);
        $this->mockPlayerRepository->method('remove')->willReturnCallback(fn() => null);
        $this->guildManager = new GuildManager(
            $this->mockSwgogGgApi,
            $this->mockEntityManagerInterface,
            $this->mockGuildRepository,
            $this->mockPlayerManager,
            $this->mockPlayerRepository,
            $this->serializer,
            $this->validatorInterface
        );
    }

    public function testFailSwgohggApi(): void
    {
        $errorSwgohApiData = [
            'error_code' => 500,
            'error_message_api_swgoh' => 'Bot diff'
        ];

        $this->mockSwgogGgApi->method('fetchGuild')
            ->willReturn($errorSwgohApiData);
        
        $caseSwgohggApiError = $this->guildManager->updateGuild(4);
        $this->assertSame($errorSwgohApiData, $caseSwgohggApiError);
    }

    public function testSchemUpdataSwgohggApi(): void
    {
        $errorUpdateSchemaApi = [
            'error_message' => 'Erreur lors de la synchronisation des informations de la guilde. Une modification de l\'API a du être faite'
        ];
        $upateBaseSwgohggData = $this->baseSwgohggData;
        $upateBaseSwgohggData['data']['guildId'] = $upateBaseSwgohggData['data']['guild_id'];
        unset($upateBaseSwgohggData['data']['guild_id']);
        $this->mockSwgogGgApi->method('fetchGuild')
            ->willReturn($upateBaseSwgohggData);
        $caseUpdateSchemaSwgohgg = $this->guildManager->updateGuild(4);
        $this->assertSame($errorUpdateSchemaApi, $caseUpdateSchemaSwgohgg);
    }

    public function testPlayersDataMissing(): void
    {
        $errorPlayersDataMissing = [
            'error_message' => 'Une erreur est survenue lors de la récupération des informations des joueurs de la guilde'
        ];
        $upateBaseSwgohggData = $this->baseSwgohggData;
        unset($upateBaseSwgohggData['data']['members']);
        $this->mockSwgogGgApi->method('fetchGuild')
            ->willReturn($upateBaseSwgohggData);
        $this->mockGuildRepository->method('findOneBy')->willReturn(null);
        $this->mockGuildRepository->method('save')->willReturnCallback(fn() => null);
        $casePlayerDataMissing = $this->guildManager->updateGuild(4);
        $this->assertSame($errorPlayersDataMissing, $casePlayerDataMissing);
        
    }

    public function testFailUpdateGuildPlayers(): void
    {
        $errorsUpdatePlayer = [
            'error_messages' => [
                'Une erreur est survenue lors de la synchronisation du joueur numéro 0'
            ]
        ];
        $this->mockSwgogGgApi->method('fetchGuild')
            ->willReturn($this->baseSwgohggData);
        $this->mockGuildRepository->method('findOneBy')->willReturn(null);
        $this->mockGuildRepository->method('save')->willReturnCallback(fn() => null);
        $this->mockPlayerManager->method('updateGuildPlayers')->willReturn($errorsUpdatePlayer);
        $caseFailUpdateGuildPlayer = $this->guildManager->updateGuild(4);
        $this->assertSame($errorsUpdatePlayer, $caseFailUpdateGuildPlayer);
    }

    public function testEverythingIsNice(): void
    {
        $everythingIsFine= true;
        $this->mockSwgogGgApi->method('fetchGuild')
            ->willReturn($this->baseSwgohggData);
        $this->mockGuildRepository->method('findOneBy')->willReturn(null);
        $this->mockGuildRepository->method('save')->willReturnCallback(fn() => null);
        $this->mockPlayerManager->method('updateGuildPlayers')->willReturn(true);
        $caseFailUpdateGuildPlayer = $this->guildManager->updateGuild(4);
        $this->assertSame($everythingIsFine, $caseFailUpdateGuildPlayer);
    }
}