<?php

namespace App\Test\Mapper;

use App\Dto\Api\Ship as ShipDto;
use App\Entity\Ship as ShipEntity;
use App\Tests\Trait\DataTrait;
use App\Utils\Mapper\Ship as ShipMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShipTest extends KernelTestCase
{
    use DataTrait;

    private static ?array $shipData = [];

    protected function setUp(): void
    {
        if (empty(self::$shipData)) {
            $this->getData('Ship');
        }
    }

    public function testShipMapper(): void
    {
        self::bootKernel();
        $ship = new ShipEntity();
        $shipDto = new ShipDto(self::$shipData);
        $ship = ShipMapper::fromDto($ship, $shipDto);
        $this->assertSame("CAPITALLEVIATHAN", $ship->getBaseId());
        $this->assertSame("Leviathan", $ship->getName());
        $this->assertSame([
            "Capital Ship",
            "Sith",
            "Sith Empire"
        ], $ship->getCategories());
        $this->assertSame("https://game-assets.swgoh.gg/textures/tex.charui_leviathan.png", $ship->getImage());
    }
}