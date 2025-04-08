<?php

namespace App\Tests\Mapper;

use App\Entity\Unit as UnitEntity;
use App\Tests\Trait\DataTrait;
use App\Entity\Player as PlayerEntity;
use App\Dto\Api\ShipPlayer as ShipPlayerDto; 
use App\Entity\ShipPlayer as ShipPlayerEntity;
use App\Utils\Mapper\ShipPlayer as ShipPlayerMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShipPlayerTest extends KernelTestCase
{
    use DataTrait;

    private static ?array $shipPlayerData = [];
    
    public function setUp(): void
    {
        if (empty(self::$shipPlayerData)) {
            $this->getData('ShipPlayer');
        }
    }

    public function testMapper() :void
    {
        self::bootKernel();

        $shipPlayer = new ShipPlayerEntity();
        $unit = $this->createMock(UnitEntity::class);
        $player = $this->createMock(PlayerEntity::class);
        $shipPlayerDto = new ShipPlayerDto(self::$shipPlayerData);

        $shipPlayer = ShipPlayerMapper::fromDto($shipPlayer, $shipPlayerDto, $player, $unit);

        $this->assertSame(85, $shipPlayer->getLevel());
        $this->assertSame(7, $shipPlayer->getNumberStars());
        $this->assertSame(84389, $shipPlayer->getGalacticalPower());
        $this->assertSame(0, $shipPlayer->getLife());
        $this->assertSame(0, $shipPlayer->getProtection());
        $this->assertSame(192, $shipPlayer->getSpeed());
    }
}