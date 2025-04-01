<?php

namespace App\Tests\Mapper;

use App\Dto\Api\Player as PlayerDto;
use App\Entity\Guild;
use App\Entity\Player as PlayerEntity;
use App\Mapper\Player as PlayerMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlayerTest extends KernelTestCase
{
    public function testPlayerMapper(): void
    {
        self::bootKernel();

        $mockGuild = $this->createMock(Guild::class);
        $mockGuild->method('getId')->willReturn(1);
        $player = new PlayerEntity();
        $playerDto = new PlayerDto(
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
        ]);

        $mappedPlayer = PlayerMapper::fromDto($player, $playerDto, $mockGuild);
        $date = new \DateTime($playerDto->last_updated);
        $this->assertSame($mockGuild, $player->getGuild());
        $this->assertSame('246639295', $player->getIdSwgoh());
        $this->assertSame("Wyøming", $player->getName());
        $this->assertSame(85, $player->getLevel());
        $this->assertSame(11395612, $player->getGalacticalPower());
        $this->assertSame(6863189, $player->getHeroesGalacticPower());
        $this->assertSame(4532423, $player->getShipsGalacticPower());
        $this->assertSame(296, $player->getGearGiven());
        $this->assertSame($date->format('Y/m/d H:i'), $player->getLastUpdate()->format('Y/m/d H:i'));
    }
}
