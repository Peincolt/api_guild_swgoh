<?php

namespace App\Tests\Mapper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Guild as GuildEntity;
use App\Dto\Api\Guild as GuildDto;
use App\Mapper\Guild as GuildMapper;

class GuildTest extends KernelTestCase
{
    public function testGuildMapper(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $guild = new GuildEntity();
        $guildDto = new GuildDto(
            [
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
                    "member_count"=> 49
                ]
            ]
        );

        $mappedGuild = GuildMapper::fromDto($guild, $guildDto);

        $this->assertSame('uuwcpRBoStWfogZersAvJA', $mappedGuild->getIdSwgoh());
        $this->assertSame('HGamers II', $mappedGuild->getName());
        $this->assertSame('526709498', $mappedGuild->getGalacticPower());
        $this->assertSame(49, $mappedGuild->getNumberPlayers());
    }
}
