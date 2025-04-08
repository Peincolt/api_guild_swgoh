<?php

namespace App\Tests\Mapper;

use App\Entity\Guild;
use App\Tests\Trait\DataTrait;
use App\Dto\Api\Player as PlayerDto;
use App\Entity\Player as PlayerEntity;
use App\Mapper\Player as PlayerMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlayerTest extends KernelTestCase
{
    use DataTrait;
    
    private static ?array $playerData = [];

    public function testPlayerMapper(): void
    {
        self::bootKernel();

        if (empty($playerData)) {
            $this->getData('Player');
        }

        $mockGuild = $this->createConfiguredMock(Guild::class,[
            'getId' => 1
        ]);
        $player = new PlayerEntity();
        $playerDto = new PlayerDto(self::$playerData);

        $mappedPlayer = PlayerMapper::fromDto($player, $playerDto, $mockGuild);
        $date = new \DateTime($playerDto->last_updated);
        $this->assertSame($mockGuild, $player->getGuild());
        $this->assertSame('246639295', $player->getIdSwgoh());
        $this->assertSame("Wyøming", $player->getName());
        $this->assertSame(85, $player->getLevel());
        $this->assertSame(11426753, $player->getGalacticalPower());
        $this->assertSame(6890288, $player->getHeroesGalacticPower());
        $this->assertSame(4536465, $player->getShipsGalacticPower());
        $this->assertSame(296, $player->getGearGiven());
        $this->assertSame($date->format('Y/m/d H:i'), $player->getLastUpdate()->format('Y/m/d H:i'));
    }
}
