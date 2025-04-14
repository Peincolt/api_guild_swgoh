<?php

namespace App\Tests\Dto\Api;

use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\Guild as GuildDto;

class GuildTest extends TestCase
{
    use DataTrait;

    private static ?array $guildData = [];
    
    public function testAbilityDtoCreation(): void
    {
        if (empty($guildData)) {
            $this->getData('Guild');
        }

        $dtoGuild = new GuildDto(self::$guildData);

        $this->assertSame('uuwcpRBoStWfogZersAvJA', $dtoGuild->id_swgoh);
        $this->assertSame('HGamers II', $dtoGuild->name);
        $this->assertSame(49, $dtoGuild->number_players);
        $this->assertSame(528125179, $dtoGuild->galactic_power);
        $this->assertSame(
            [
                [
                    "galactic_power" => 10973154,
                    "guild_join_time" => "2018-01-08T19:04:43Z",
                    "lifetime_season_score" => 603364,
                    "member_level"=> 2,
                    "ally_code"=> 842173848,
                    "player_level"=> 85,
                    "player_name"=> "Jay Jay",
                    "league_id"=> "KYBER",
                    "league_name"=> "Kyber",
                    "league_frame_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_portrait_league_kyber.png",
                    "portrait_image"=> "https://game-assets.swgoh.gg/textures/tex.vanity_greedo.png",
                    "title"=> "Spice Freighter Navigator",
                    "squad_power"=> 178396
                ]

            ]
        , $dtoGuild->members);
    }
}
