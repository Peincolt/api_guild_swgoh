<?php

namespace App\Tests\Dto\Api;

use App\Dto\Api\Guild as GuildDto;
use PHPUnit\Framework\TestCase;

class GuildTest extends TestCase
{
    public function testAbilityDtoCreation(): void
    {
        $fakeDataApi = [
            "guild_id"=> "uuwcpRBoStWfogZersAvJA",
            "name"=> "HGamers II",
            "external_message"=> "Guilde actif.Discord obligatoire(https://discord.gg/vZ8AeM3) naboo caisse 90M. BT ROTE 33*",
            "banner_color_id"=> "cyan_purple",
            "banner_logo_id"=> "guild_icon_blast",
            "enrollment_status"=> 2,
            "galactic_power"=> 526709498,
            "guild_type"=> null,
            "level_requirement"=> 85,
            "member_count"=> 49
        ];

        $dtoGuild = new GuildDto($fakeDataApi);

        $this->assertSame('uuwcpRBoStWfogZersAvJA', $dtoGuild->id_swgoh);
        $this->assertSame('HGamers II', $dtoGuild->name);
        $this->assertSame(49, $dtoGuild->number_players);
        $this->assertSame(526709498, $dtoGuild->galactic_power);
    }
}
