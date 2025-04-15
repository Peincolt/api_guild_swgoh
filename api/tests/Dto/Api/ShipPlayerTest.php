<?php

namespace App\Tests\Dto\Api;

use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\ShipPlayer as DtoShipPlayer;

class ShipPlayerTest extends TestCase
{
    use DataTrait;

    private static ?array $shipPlayerData = [];
    
    public function testShipPlayerCreation(): void
    {
        if (empty(self::$shipPlayerData)) {
            $this->getData('ShipPlayer');
        }

        $dtoShipPlayer = new DtoShipPlayer(self::$shipPlayerData);
        $this->assertSame(85, $dtoShipPlayer->level);
        $this->assertSame(7, $dtoShipPlayer->number_stars);
        $this->assertSame(84389, $dtoShipPlayer->galactical_power);
        $this->assertSame(0, $dtoShipPlayer->life);
        $this->assertSame(0, $dtoShipPlayer->protection);
        $this->assertSame(192, $dtoShipPlayer->speed);
        $this->assertSame(2, $dtoShipPlayer->combat_type);
    }
}