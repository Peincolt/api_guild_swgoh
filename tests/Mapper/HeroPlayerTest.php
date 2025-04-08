<?php

namespace App\Tests\Mapper;


use App\Entity\Unit as UnitEntity;
use App\Tests\Trait\DataTrait;
use App\Tests\Utils\JsonFileLoader;
use App\Entity\Player as PlayerEntity;
use App\Dto\Api\HeroPlayer as HeroPlayerDto;
use App\Entity\HeroPlayer as HeroPlayerEntity;
use App\Utils\Mapper\HeroPlayer as HeroPlayerMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeroPlayerTest extends KernelTestCase
{
    use DataTrait;
    
    private static ?array $heroPlayerData = [];

    public function setUp(): void
    {
        if (empty(self::$heroPlayerData)) {
            $this->getData('HeroPlayer');
        }
    }

    public function testHeroPlayerMapper(): void
    {
        self::bootKernel();

        $heroPlayer = new HeroPlayerEntity();
        $unit = $this->createMock(UnitEntity::class);
        $player = $this->createMock(PlayerEntity::class);
        $heroPlayerDto = new HeroPlayerDto(self::$heroPlayerData);

        $heroPlayer = HeroPlayerMapper::fromDto($heroPlayer, $heroPlayerDto, $player, $unit);

        $this->assertSame(85, $heroPlayer->getLevel());
        $this->assertSame(7, $heroPlayer->getNumberStars());
        $this->assertSame(49710, $heroPlayer->getGalacticalPower());
        $this->assertSame(113228, $heroPlayer->getLife());
        $this->assertSame(185355, $heroPlayer->getProtection());
        $this->assertSame(544, $heroPlayer->getSpeed());
        $this->assertSame(9, $heroPlayer->getRelicLevel());
        $this->assertSame(13, $heroPlayer->getGearLevel());
    }
}
