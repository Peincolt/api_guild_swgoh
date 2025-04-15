<?php

namespace App\Tests\Mapper;

use App\Tests\Trait\DataTrait;
use App\Dto\Api\Guild as GuildDto;
use App\Entity\Guild as GuildEntity;
use App\Mapper\Guild as GuildMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GuildTest extends KernelTestCase
{
    use DataTrait;

    private static ?array $guildData = [];

    public function testGuildMapper(): void
    {
        self::bootKernel();

        if(empty(self::$guildData)) {
            $this->getData('Guild');
        }

        $guild = new GuildEntity();
        $guildDto = new GuildDto(self::$guildData);
        $mappedGuild = GuildMapper::fromDto($guild, $guildDto);

        $this->assertSame('uuwcpRBoStWfogZersAvJA', $mappedGuild->getIdSwgoh());
        $this->assertSame('HGamers II', $mappedGuild->getName());
        $this->assertSame('528125179', $mappedGuild->getGalacticPower());
        $this->assertSame(49, $mappedGuild->getNumberPlayers());
    }
}
