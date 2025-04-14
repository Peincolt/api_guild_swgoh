<?php

namespace App\Test\Dto\Api;

use App\Tests\Trait\DataTrait;
use PHPUnit\Framework\TestCase;
use App\Dto\Api\Ship as ShipDto;

class ShipTest extends TestCase
{
    use DataTrait;

    private static ?array $shipData = [];
    
    public function testCreationShipDto(): void
    {
        if (empty(self::$shipData)) {
            $this->getData('Ship');
        }

        $shipDto = new ShipDto(self::$shipData);
        $this->assertSame("CAPITALLEVIATHAN", $shipDto->base_id);
        $this->assertSame("Leviathan", $shipDto->name);
        $this->assertSame([
            "Capital Ship",
            "Sith",
            "Sith Empire"
        ], $shipDto->categories);
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png", $shipDto->image);
    }
}