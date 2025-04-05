<?php

namespace App\Tests\Dto\Api;

use App\Dto\Api\Guild as GuildDto;
use PHPUnit\Framework\TestCase;

class GuildTest extends TestCase
{
    public function testAbilityDtoCreation(): void
    {
        $fakeDataApi = [
            "data" => [
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
                        "galactic_power"=> 11399151,
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

        $dtoGuild = new GuildDto($fakeDataApi);

        $this->assertSame('uuwcpRBoStWfogZersAvJA', $dtoGuild->id_swgoh);
        $this->assertSame('HGamers II', $dtoGuild->name);
        $this->assertSame(49, $dtoGuild->number_players);
        $this->assertSame(526709498, $dtoGuild->galactic_power);
        $this->assertSame(
            [
                [
                    "galactic_power"=> 11399151,
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
        , $dtoGuild->members);
    }
}
