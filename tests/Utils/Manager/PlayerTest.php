<?php

namespace App\Tests\Utils\Manager;

use App\Entity\Guild;
use App\Dto\Api\PlayerDto;
use App\Entity\PlayerEntity;
use PHPUnit\Framework\TestCase;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Manager\Player as PlayerManager;
use App\Utils\Service\Api\SwgohGg as SwgohGgApi;
use App\Utils\Manager\HeroPlayer as HeroPlayerManager;
use App\Utils\Manager\ShipPlayer as ShipPlayerManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerTest extends KernelTestCase
{
    private Guild $mockGuild;
    private PlayerManager $playerManager;
    private SwgohGgApi $mockSwgogGgApi;
    private HeroPlayerManager $mockHeroPlayerManager;
    private ShipPlayerManager $mockShipPlayerManager;
    private PlayerRepository $mockPlayerRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validatorInterface;
    private string $allyCode;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->allyCode = "xxxx";
        $this->baseSwgohggData =
            [
                "data"=> [
                "ally_code"=> 246639295,
                "arena_leader_base_id"=> "GLLEIA",
                "arena_rank"=> 118,
                "level"=> 85,
                "name"=> "Wyøming",
                "last_updated"=> "2025-03-31T17:19:41.220361",
                "galactic_power"=> 11395612,
                "character_galactic_power"=> 6863189,
                "ship_galactic_power"=> 4532423,
                "ship_battles_won"=> 1439,
                "pvp_battles_won"=> 3654,
                "pve_battles_won"=> 356766,
                "pve_hard_won"=> 109642,
                "galactic_war_won"=> 33949,
                "guild_raid_won"=> 1574,
                "guild_contribution"=> 2745312,
                "guild_exchange_donations"=> 296,
                "season_full_clears"=> 243,
                "season_successful_defends"=> 1310,
                "season_league_score"=> 734060,
                "season_undersized_squad_wins"=> 954,
                "season_promotions_earned"=> 70,
                "season_banners_earned"=> 756146,
                "season_offensive_battles_won"=> 4545,
                "season_territories_defeated"=> 1350,
                "url"=> "/p/246639295/",
                "arena"=> [
                    "rank"=> 118,
                    "leader"=> "GLLEIA",
                    "members"=> [
                        "R2D2_LEGENDARY",
                        "CAPTAINDROGAN",
                        "CAPTAINREX",
                        "OLDBENKENOBI"
                    ]
                ],
                "fleet_arena"=> [
                    "rank"=> 16,
                    "leader"=> "CAPITALLEVIATHAN",
                    "members"=> [
                        "SITHBOMBER",
                        "FURYCLASSINTERCEPTOR",
                        "SITHFIGHTER"
                    ],
                    "reinforcements"=> [
                        "SITHSUPREMACYCLASS",
                        "TIEDAGGER",
                        "SITHINFILTRATOR"
                    ]
                ],
                "skill_rating"=> 2807,
                "league_name"=> "Chromium",
                "league_frame_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_portrait_league_chromium.png",
                "league_blank_image"=> "https://game-assets.swgoh.gg/textures/tex.league_icon_chromium_blank.png",
                "league_image"=> "https://game-assets.swgoh.gg/textures/tex.league_icon_chromium.png",
                "division_number"=> 1,
                "division_image"=> "https://game-assets.swgoh.gg/textures/_1.png",
                "portrait_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_kalani.png",
                "title"=> "Bitter Pill Company",
                "guild_id"=> "uuwcpRBoStWfogZersAvJA",
                "guild_name"=> "HGamers II",
                "guild_url"=> "/g/uuwcpRBoStWfogZersAvJA/",
                "mods"=> []
            ]
        ];

        $this->validatorInterface = $container->get(ValidatorInterface::class);
        $this->serializer = $container->get(SerializerInterface::class);
        $this->mockSwgogGgApi = $this->createMock(SwgohGgApi::class);
        $this->mockHeroPlayerManager = $this->createMock(HeroPlayerManager::class);
        $this->mockShipPlayerManager = $this->createMock(ShipPlayerManager::class);
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);
        $this->mockGuild = $this->createMock(Guild::class);
        $this->playerManager = new PlayerManager(
            $this->createMock(EntityManagerInterface::class),
            $this->mockSwgogGgApi,
            $this->mockHeroPlayerManager,
            $this->mockShipPlayerManager,
            $this->mockPlayerRepository,
            $this->serializer,
            $this->validatorInterface
        );
    }

    public function testFailSwgohggApi()
    {
        $errorSwgohApiData = [
            'error_code' => 500,
            'error_message_api_swgoh' => 'Sup diff'
        ];

        $this->mockSwgogGgApi->method('fetchPlayer')
            ->willReturn($errorSwgohApiData);
        $caseSwgohggApiError = $this->playerManager->updatePlayerWithApi($this->allyCode, $this->mockGuild);

        $this->assertEquals('Sup diff', $caseSwgohggApiError['error_message_api_swgoh']);
    }
}